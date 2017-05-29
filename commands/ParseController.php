<?php

namespace app\commands;

use app\services\LogParser;
use yii\console\Controller;
/**
 * Log parser.
 */
class ParseController extends Controller
{
    /**
     * This command parses your log file.
     * @param string $file the message to be echoed.
     */

    public function actionParse($file)
    {
        $model = new LogParser();
        $rows = $model->indexFile($file);
        $path_parts = pathinfo($file);
        $cutname = $path_parts['basename'];
        $model->logUploadThroughConsole($rows, $cutname);
        echo 'Successeded'. "\n";
    }
}