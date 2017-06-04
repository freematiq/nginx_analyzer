<?php

namespace app\services;

use app\models\Logs;
use app\models\QueryTypes;
use app\models\UploadHistory;
use app\models\UserAgents;
use Throwable;
use Yii;
use yii\db\Exception;

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

        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            foreach ($rows as $row => $data) {

                preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
                $string = $matches[0][5];
                $string = str_replace('"', '', $string);
                $row_data_edited = explode(' ', $string);
                $row_data = $matches;

                $types = QueryTypes::find()->where(['query_type' => $row_data_edited[0]])->one();
                if (is_null($types)) {
                    $types = new QueryTypes();
                    if (empty($row_data_edited[0])) {
                        $types->query_type = "EMPTY";
                    } else
                        $types->query_type = $row_data_edited[0];
                    $types->save();
                }

                $useragents = UserAgents::find()->where(['browser_info' => str_replace('"', '', $row_data[0][10])])->one();
                if (is_null($useragents)) {
                    $useragents = new UserAgents();
                    $useragents->browser_info = str_replace('"', '', $row_data[0][10]);
                    $useragents->save();
                }
                //$useragents = new UserAgents();
                //$service = new LogParserService();
                //$service->fillingTableLogs($row_data[0][10]);

                $logs = new Logs();
                $logs->query_type = $types->query_type_id;
                $logs->sip = $row_data[0][0];
                $logs->query_date = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
                if (empty($row_data_edited[0])) {
                    $logs->url_query = null;
                    $type_id = QueryTypes::find()->select('query_type_id')
                        ->where(['query_type' => "EMPTY"])->one();
                    $logs->query_type = $type_id->query_type_id;
                } else {
                    if (empty($row_data_edited[1])) {
                        $logs->url_query = null;
                    } else {
                        if (substr_count($row_data_edited[1], '?') === 0) {
                            $logs->url_query = $row_data_edited[1];
                        } elseif (substr_count($row_data_edited[1], '?') > 0) {
                            $logs->url_query = stristr($row_data_edited[1], '?', true);
                        }
                    }
                }
                $logs->query_code = $row_data[0][6];
                $logs->query_size = $row_data[0][7];
                $logs->query_time_float = $row_data[0][8];
                $logs->query_time_numeric = $row_data[0][8];

                if (substr_count($row_data[0][9], '?') === 0) {
                    $logs->quested_page = $row_data[0][9];
                } elseif (substr_count($row_data[0][9], '?') > 0) {
                    $logs->quested_page = stristr($row_data[0][9], '?', true);
                }
                // $logs->quested_page = str_replace('"', '', $row_data[0][9]);

                if (str_replace('"', '', $row_data[0][11]) != "-") {
                    if (substr_count($row_data[0][11], ',') === 0) {
                        $logs->user_ip = str_replace('"', '', $row_data[0][11]);
                    } elseif (substr_count($row_data[0][11], ',') > 0) {
                        $period = str_replace('"', '', $row_data[0][11]);
                        $period2 = stristr($period, ',', false);
                        $logs->user_ip = stristr($period, ',', true);
                        $logs->user_ip_reserve = str_replace(",", '', $period2);
                    }
                } elseif (str_replace('"', '', $row_data[0][11]) === "-") {
                    $logs->user_ip = $row_data[0][0];
                }
                $logs->uploaded_file = $file_id;
                $logs->browser_info = $useragents->user_agent_id;
                $logs->save();
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

        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            foreach ($rows as $row => $data) {
                preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $data, $matches);
                $string = $matches[0][5];
                $string = str_replace('"', '', $string);
                $row_data_edited = explode(' ', $string);
                $row_data = $matches;

                $types = QueryTypes::find()->where(['query_type' => $row_data_edited[0]])->one();
                if (is_null($types)) {
                    $types = new QueryTypes();
                    if (empty($row_data_edited[0])) {
                        $types->query_type = "EMPTY";
                    } else
                        $types->query_type = $row_data_edited[0];
                    $types->save();
                }

                $useragents = UserAgents::find()->where(['browser_info' => str_replace('"', '', $row_data[0][10])])->one();
                if (is_null($useragents)) {
                    $useragents = new UserAgents();
                    $useragents->browser_info = str_replace('"', '', $row_data[0][10]);
                    $useragents->save();
                }

                $logs = new Logs();
                $logs->query_type = $types->query_type_id;
                $logs->sip = $row_data[0][0];
                $logs->query_date = str_replace('[', '', $row_data[0][3]) . str_replace(']', '', $row_data[0][4]);
                if (empty($row_data_edited[0])) {
                    $logs->url_query = null;
                    $type_id = QueryTypes::find()->select('query_type_id')
                        ->where(['query_type' => "EMPTY"])->one();
                    $logs->query_type = $type_id->query_type_id;
                } else {
                    if (empty($row_data_edited[1])) {
                        $logs->url_query = null;
                    } else {
                        if (substr_count($row_data_edited[1], '?') === 0) {
                            $logs->url_query = $row_data_edited[1];
                        } elseif (substr_count($row_data_edited[1], '?') > 0) {
                            $logs->url_query = stristr($row_data_edited[1], '?', true);
                        }
                    }
                }
                $logs->query_code = $row_data[0][6];
                $logs->query_size = $row_data[0][7];
                $logs->query_time_float = $row_data[0][8];
                $logs->query_time_numeric = $row_data[0][8];

                if (substr_count($row_data[0][9], '?') === 0) {
                    $logs->quested_page = $row_data[0][9];
                } elseif (substr_count($row_data[0][9], '?') > 0) {
                    $logs->quested_page = stristr($row_data[0][9], '?', true);
                }

                //$logs->quested_page = str_replace('"', '', $row_data[0][9]);
                if (str_replace('"', '', $row_data[0][11]) != "-") {
                    if (substr_count($row_data[0][11], ',') === 0) {
                        $logs->user_ip = str_replace('"', '', $row_data[0][11]);
                    } elseif (substr_count($row_data[0][11], ',') > 0) {
                        $period = str_replace('"', '', $row_data[0][11]);
                        $period2 = stristr($period, ',', false);
                        $logs->user_ip = stristr($period, ',', true);
                        $logs->user_ip_reserve = str_replace(",", '', $period2);
                    }
                } elseif (str_replace('"', '', $row_data[0][11]) === "-") {
                    $logs->user_ip = $row_data[0][0];
                }
                $logs->uploaded_file = $uploadedfile->filename_id;
                $logs->browser_info = $useragents->user_agent_id;
                $logs->save();
            }
            $transaction->commit();
        } catch (\Throwable $exception) {
            $transaction->rollBack();
            throw $exception;
        }
        return 0;
    }

    public function fillingTableLogs($browser_info)
    {
        $useragents = UserAgents::find()->where(['browser_info' => str_replace('"', '', $browser_info)])->one();
        if (is_null($useragents)) {
            $useragents = new UserAgents();
            $useragents->browser_info = str_replace('"', '', $browser_info);
            $useragents->save();
        }
        return 0;
    }
}