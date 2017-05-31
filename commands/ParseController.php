<?php

namespace app\commands;

use app\services\LogParserService;
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
        if (file_exists($file)) {
            $parser = new LogParserService();
            $rows = $parser->fileToParse($file);
            $path_parts = pathinfo($file);
            $filename = $path_parts['basename'];
            $parser->logUploadThroughConsole($rows, $filename);
            echo 'Successeded'. "\n";
        } else {
            echo "Failure. $file doesn't exist. Try another name or path to file.". "\n";
        }

    }
}