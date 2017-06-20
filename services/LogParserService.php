<?php

namespace app\services;

use app\models\Logs;
use app\models\QueryTypes;
use app\models\UploadHistory;
use app\models\UserAgents;
use Yii;

/**
 * Class LogParserService
 * @package app\services
 * @property LogParserService $parser The parser component. This property is read-only.
 */
class LogParserService
{
    /**
     * This method imports data from file into array $rows
     * @param string $path file for parsing
     * @return array $rows array with every string in private cell
     */
    public function fileToParse($path)
    {
        /*
         * получаем файл, удаляем из него все пустые строки.
         * оставшиеся строки записывваем в массив.
         */
        $file = file_get_contents($path);
        echo "Я загрузил файл и потратил: " . round(memory_get_usage($real_usage = true) / 1000000, 3) . " МБ" . "\n";
        $file = trim(preg_replace('/[\r\n]+/m', "\n", $file));
        $rows = explode("\n", $file);
        return $rows;
    }

    /**
     * This method inserts data from incoming array into DB through UploadFile
     * @param array $rows file with logs converted into array
     * @param int $file_id id of current uploaded file in table upload_history
     * @return bool returns 0 if succeeded
     * @throws \Throwable if uploading fails wait for rollback
     */
    public function logUploadThroughBrowser($rows, $file_id)
    {
        /*
         * в случае ошибки при заполнении данными происходит откат для таблиц
         * logs, query_types, user_agents.
         * в случае возниконвения ошибок при разборе новых строк подставлять проблемную строку напрямую в следующий код:
         *  <?php
            $data = '';
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
            var_dump($matches);
            ?>
         */
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            /*
             * разбор файла по строкам
             */
            $collector = [];
            foreach ($rows as $row => $data) {
                list ($row_data_edited, $row_data) = $this->dataPreparer($data);
                if ((is_numeric($row_data[0][8]) === true) && (empty($row_data[0][11]) === false)) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseOne($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $file_id);
                    $this->dataSaver($collector);
                    $collector=[];
                }
                if ((is_numeric($row_data[0][8]) === true) && (empty($row_data[0][11]) === true)) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseThree($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $file_id);
                    $this->dataSaver($collector);
                    $collector=[];
                }
                if (is_numeric($row_data[0][8]) === false) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseTwo($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $file_id);
                    $this->dataSaver($collector);
                    $collector=[];
                }
            }
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
        Yii::$app->session->setFlash('success', "Файл добавлен в БД");
        return 0;
    }

    /**
     * This method inserts data from incoming array into DB through console
     * @param array $rows file with logs converted into array
     * @param string $filename the name of the parsing file without path
     * @return bool returns 0 if succeeded
     * @throws \Throwable if uploading fails wait for rollback
     */
    public function logUploadThroughConsole($rows, $filename)
    {
        $uploadedfile = new UploadHistory();
        $uploadedfile->filename = $filename;
        $uploadedfile->save();
        /*
         * в случае ошибки при заполнении данными происходит откат для таблиц
         * logs, query_types, user_agents.
         * в случае возниконвения ошибок при разборе новых строк подставлять проблемную строку напрямую в следующий код:
         *  <?php
            $data = '';
            preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
            var_dump($matches);
            ?>
         */
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            /*
             * разбор файла по строкам
             * строки разделяются на два типа - там, где время запроса пришло, и там, где не пришло.
             * каждая отработанная строка записывается в накопитель, который после того, как строки в файле кончатся, уйдет на запись в БД.
             */
            $collector = [];
            $counter=0;
            foreach ($rows as $row => $data) {
                list ($row_data_edited, $row_data) = $this->dataPreparer($data);
                if ((is_numeric($row_data[0][8]) === true) && (empty($row_data[0][11]) === false)) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseOne($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $uploadedfile->filename_id);
                    $this->dataSaver($collector);
                    $collector=[];
                    $counter++;
                }
                if ((is_numeric($row_data[0][8]) === true) && (empty($row_data[0][11]) === true)) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseThree($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $uploadedfile->filename_id);
                    $this->dataSaver($collector);
                    $collector=[];
                    $counter++;
                }
                if (is_numeric($row_data[0][8]) === false) {
                    list($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip) = $this->variableRewriterCaseTwo($row_data_edited, $row_data);
                    $collector[] = $this->dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $uploadedfile->filename_id);
                    $this->dataSaver($collector);
                    $collector=[];
                    $counter++;
                }
            }
            echo "Я заполнил массив и потратил на это: " . round(memory_get_usage($real_usage = true) / 1000000, 3) . " МБ" . "\n";
            echo "Я обработал " . $counter . " строк" . "\n";
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
        return 0;
    }

    /**
     * This method writes the parts of row in two arrays. Read more below in return section
     * @param string $data rows of parsing file
     * @return array [$row_data_edited, $row_data], first variable returns array with query_type and url_query, second variable returns array with another data
     */
    public function dataPreparer($data)
    {
        /*
         * с помощью регулярного выражения разбираем строку в массив.
         * ожидается 12 элементов в массиве
         */
        preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
        /*
         * в отдельную переменную записываем тип запроса, url запроса, тип протокола и его версия
         */
        $string = $matches[0][5];
        /*
         * удаляем знаки "
         */
        $string = trim($string, '\"');
        /*
         * ожидается три элемента в массиве, третий в дальнейшем не используется
         */
        $row_data_edited = explode(' ', $string);
        /*
         * переинициализация массива
         */
        $row_data = $matches;

        return [$row_data_edited, $row_data];

    }

    /**
     * This method rewrites variables from prepared arrays in case when time of query exists in incoming data
     * @param array $row_data_edited array with query_type and url_query
     * @param array $row_data array with another data
     * @return array array of variables:  server's ip - the date of query - the type of query - the incoming url of query - the code of query - the size of query - the time of query - the url of page that was quested - user agents - user's ip
     */
    public function variableRewriterCaseOne($row_data_edited, $row_data)
    {
        /*
         * элементам массива с данными присваиваются говорящие имена, которые соответсвуют
         * названиям столбцов в таблицах, но имеют приставку v_ для лучшей читаемости.
         * элементы с индексами [0][1], [0][2] всегда не используются. Тип запроса и url вместе из тех соображений,
         * что бывает, что ни того, ни другого нет.
         */
        $v_sip = $row_data[0][0];
        $v_query_date = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
        $v_query_type_with_url_query = $row_data_edited;
        $v_query_code = $row_data[0][6];
        $v_query_size = $row_data[0][7];
        $v_query_time = $row_data[0][8];
        $v_quested_page = $row_data[0][9];
        $v_browser_info = $row_data[0][10];
        $v_user_ip = $row_data[0][11];

        return [
            $v_sip,
            $v_query_date,
            $v_query_type_with_url_query,
            $v_query_code,
            $v_query_size,
            $v_query_time,
            $v_quested_page,
            $v_browser_info,
            $v_user_ip
        ];
    }

    /**
     * This method rewrites variables from prepared arrays in case when time of query does not exist in incoming data
     * @param $row_data_edited array with query_type and url_query
     * @param $row_data array with another data
     * @return array array of variables:  server's ip - the date of query - the type of query - the incoming url of query - the code of query - the size of query - the time of query (NULL) - the url of page that was quested - user agents - user's ip
     */
    public function variableRewriterCaseTwo($row_data_edited, $row_data)
    {
        /*
         * элементам массива с данными присваиваются говорящие имена, которые соответсвуют
         * названиям столбцов в таблицах, но имеют приставку v_ для лучшей читаемости.
         * элементы с индексами [0][1], [0][2] всегда не используются. Тип запроса и url вместе из тех соображений,
         * что бывает, что ни того, ни другого нет.
         */
        $v_sip = $row_data[0][0];
        $v_query_date = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
        $v_query_type_with_url_query = $row_data_edited;
        $v_query_code = $row_data[0][6];
        $v_query_size = $row_data[0][7];
        $v_query_time = Null;
        $v_quested_page = $row_data[0][8];
        $v_browser_info = $row_data[0][9];
        $v_user_ip = $row_data[0][0];

        return [
            $v_sip,
            $v_query_date,
            $v_query_type_with_url_query,
            $v_query_code,
            $v_query_size,
            $v_query_time,
            $v_quested_page,
            $v_browser_info,
            $v_user_ip
        ];
    }

    /**
     * This method rewrites variables from prepared arrays in case when time of query exists in incoming data, but user's ip ain't come
     * @param $row_data_edited array with query_type and url_query
     * @param $row_data array with another data
     * @return array array of variables:  server's ip - the date of query - the type of query - the incoming url of query - the code of query - the size of query - the time of query (NULL) - the url of page that was quested - user agents - user's ip
     */
    public function variableRewriterCaseThree($row_data_edited, $row_data)
    {
        /*
         * элементам массива с данными присваиваются говорящие имена, которые соответсвуют
         * названиям столбцов в таблицах, но имеют приставку v_ для лучшей читаемости.
         * элементы с индексами [0][1], [0][2] всегда не используются. Тип запроса и url вместе из тех соображений,
         * что бывает, что ни того, ни другого нет.
         */
        $v_sip = $row_data[0][0];
        $v_query_date = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
        $v_query_type_with_url_query = $row_data_edited;
        $v_query_code = $row_data[0][6];
        $v_query_size = $row_data[0][7];
        $v_query_time = $row_data[0][8];
        $v_quested_page = $row_data[0][9];
        $v_browser_info = $row_data[0][10];
        $v_user_ip = $row_data[0][0];

        return [
            $v_sip,
            $v_query_date,
            $v_query_type_with_url_query,
            $v_query_code, $v_query_size,
            $v_query_time, $v_quested_page,
            $v_browser_info,
            $v_user_ip
        ];
    }

    /**
     * This method is general and contains all logic for collecting data for further inserting
     * @param string $v_sip server's ip
     * @param string $v_query_date the date of query
     * @param string $v_query_type_with_url_query the type of query and url of it
     * @param int $v_query_code the code of query
     * @param int $v_query_size the size of query
     * @param float $v_query_time the time of query
     * @param string $v_quested_page the page that was quested
     * @param string $v_browser_info user agents
     * @param string $v_user_ip user's ip
     * @param int $file_id id of uploaded file
     * @return array array of data for inserting into db
     */
    public function dataCollector($v_sip, $v_query_date, $v_query_type_with_url_query, $v_query_code, $v_query_size, $v_query_time, $v_quested_page, $v_browser_info, $v_user_ip, $file_id)
    {
        /*
         * заполняется таблица query_types. Если тип запроса ещё не существует,
         * то он будет добавлен. Если тип запроса пришел пустым, то будет установлено значение EMPTY.
         */
        $types = QueryTypes::find()->where(['query_type' => $v_query_type_with_url_query[0]])->one();
        if (is_null($types)) {
            $types = new QueryTypes();
            if (empty($v_query_type_with_url_query[0])) {
                $types->query_type = "EMPTY";
            } else
                $types->query_type = $v_query_type_with_url_query[0];
            $types->save();
        }
        /*
         * заполняется таблица user_agents. дополнительно удаляются символы ".
         */
        $key = trim($v_browser_info, '\"');
        $useragent = UserAgents::find()->where(['browser_info' => $key])->one();
        if (is_null($useragent)) {
            $useragent = new UserAgents();
            $useragent->browser_info = $key;
            $useragent->save();
        }
        /*
         * заполняется таблица logs.
         * query_type, browser_info приходящие идентификаторы внешних ключей.
         * file_id - идентификатор внешнего ключа загружаемого файла - входящее значение функции.
         */
        $logs = new Logs();
        $logs->query_type = $types->query_type_id;
        $logs->sip = $v_sip;
        $logs->query_date = $v_query_date;
        /*
         * если в строке не было типа запроса, то присваиваем полю query_type id строки с EMPTY,
         * а в url_query оставляем null.
         * если тип запроса был, то отрезаем get-параметры, если они есть, для поля url_query.
         */
        if (empty($v_query_type_with_url_query[0])) {
            $logs->url_query = null;
            $type_id = QueryTypes::find()->select('query_type_id')
                ->where(['query_type' => "EMPTY"])->one();
            $logs->query_type = $type_id->query_type_id;
        } else {
            if (empty($v_query_type_with_url_query[1])) {
                $logs->url_query = null;
            } else {
                if (substr_count($v_query_type_with_url_query[1], '?') === 0) {
                    $logs->url_query = $v_query_type_with_url_query[1];
                } elseif (substr_count($v_query_type_with_url_query[1], '?') > 0) {
                    $logs->url_query = stristr($v_query_type_with_url_query[1], '?', true);
                }
            }
        }
        $logs->query_code = $v_query_code;
        $logs->query_size = $v_query_size;
        /*
         * время запроса хранится и как float, и как numeric(19,3).
         */
        $logs->query_time_float = $v_query_time;
        $logs->query_time_numeric = $v_query_time;
        /*
         * убираем get-параметры для quested_page, если они есть.
         */
        if (substr_count($v_quested_page, '?') === 0) {
            $logs->quested_page = $v_quested_page;
        } elseif (substr_count($v_quested_page, '?') > 0) {
            $logs->quested_page = stristr($v_quested_page, '?', true);
        }
        /*
         * если ip пользователя не пришел, то оставляем для этого поля ip сервиса.
         * если пришло два ip пользователя,
         * то при наличии запятой между адресами первый адрес записываем в поле user_ip,
         * а второй адрес в резервный столбец user_ip_reserve.
         */
        $logs->uploaded_file = $file_id;
        $logs->browser_info = $useragent->user_agent_id;
        if (str_replace('"', '', $v_user_ip) != "-") {
            if (substr_count($v_user_ip, ',') === 0) {
                $logs->user_ip = str_replace('"', '', $v_user_ip);
            } elseif (substr_count($v_user_ip, ',') > 0) {
                $period = str_replace('"', '', $v_user_ip);
                $period2 = stristr($period, ',', false);
                $logs->user_ip = stristr($period, ',', true);
                $logs->user_ip_reserve = str_replace(",", '', $period2);
            }
        } elseif (str_replace('"', '', $v_user_ip) === "-") {
            $logs->user_ip = $v_sip;
        }
        return [
            $logs->sip,
            $logs->query_date,
            $logs->query_type,
            $logs->url_query,
            $logs->query_code,
            $logs->query_size,
            $logs->quested_page,
            $logs->browser_info,
            $logs->user_ip,
            $logs->uploaded_file,
            $logs->query_time_float,
            $logs->query_time_numeric,
            $logs->user_ip_reserve
        ];
    }

    /**
     * This method saves data into DB
     * @param array $collector array of collected data for 1 row
     * @return bool returns 0 if success
     */
    private function dataSaver($collector)
    {
        Yii::$app->db->createCommand()->batchInsert(Logs::tableName(), [
            'sip',
            'query_date',
            'query_type',
            'url_query',
            'query_code',
            'query_size',
            'quested_page',
            'browser_info',
            'user_ip',
            'uploaded_file',
            'query_time_float',
            'query_time_numeric',
            'user_ip_reserve'
        ], $collector)->execute();
        return 0;
    }
}