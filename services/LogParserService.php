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
     * This method imports data from file into array $rows deleting
     * empty last cell of array
     *
     * @param string $path file for parsing
     * @return array array with every string in private cell
     */
    public function fileToParse($path)
    {
        /*
         * получаем файл, удаляем из него все пустые строки.
         * оставшиеся строки записывваем в массив.
         */
        $file = file_get_contents($path);
        $file = trim(preg_replace('/[\r\n]+/m', "\n", $file));
        $rows = explode("\n", $file);
        return $rows;
    }

    /**
     * This method inserts data from incoming array into DB through UploadFile
     *
     * @param array $rows file with logs converted into array
     * @param int $file_id id of current uploaded file in table upload_history
     * @return bool returns 0 if succeeded
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
            foreach ($rows as $row => $data) {
                $this->fillingTables($data, $file_id);
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
     *
     * @param array $rows file with logs converted into array
     * @param string $filename the name of the parsing file without path
     * @return bool returns 0 if succeeded
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
             */
            foreach ($rows as $row => $data) {
                $this->fillingTables($data, $uploadedfile->filename_id);
                }
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
        return 0;
    }

    /**
     * This method contains all logic for inserting data into tables logs, query_types, upload_history.
     *
     * @param string $data rows of parsing file
     * @param int $file_id filename_id from table upload_history
     * @return int returns 0 if succeeded
     */
    public function fillingTables($data, $file_id)
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
        $string = str_replace('"', '', $string);
        /*
         * ожидается три элемента в массиве, третий в дальнейшем не используется
         */
        $row_data_edited = explode(' ', $string);
        /*
         * переинициализация массива
         */
        $row_data = $matches;
        /*
         * элементам массива с данными присваиваются говорящие имена, которые соответсвуют
         * названиям столбцов в таблицах, но имеют приставку v_ для лучшей читаемости.
         * элементы с индексами [0][1], [0][2] всегда не используются
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

        $useragents = UserAgents::find()->where(['browser_info' => str_replace('"', '', $v_browser_info)])->one();
        if (is_null($useragents)) {
            $useragents = new UserAgents();
            $useragents->browser_info = str_replace('"', '', $v_browser_info);
            $useragents->save();
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
        $logs->uploaded_file = $file_id;
        $logs->browser_info = $useragents->user_agent_id;
        $logs->save();
        return 0;
    }
}