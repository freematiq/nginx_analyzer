<?php

namespace app\commands;

use yii\console\Controller;

/**
 * Nginx log parser.
 */
class ParseController extends Controller
{
    /**
     * This command parses your log file.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }
}