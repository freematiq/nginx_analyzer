<?php

namespace app\commands;

use app\controllers\ParsingController;
use yii\console\Controller;
use app\models\Logs;
use app\models\UploadHistory;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UploadForm;
use yii\web\UploadedFile;


class ParseController extends Controller
{
    /*
     * This command parses your log file.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $model = new Logs();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $model->file->saveAs('/home/jaroslav/basic/web/uploads' . $model->file->baseName . '.' . $model->file->extension);
                $model->logUpload();
            }
        }

        return $this->render('index', ['model' => $model]);
    }
}