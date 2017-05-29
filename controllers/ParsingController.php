<?php

namespace app\controllers;

use app\models\Logs;
use app\models\UploadHistory;
use app\services\LogParser;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class ParsingController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Logs();
        $filename = new UploadHistory();
        $service = new LogParser();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $model->file->saveAs($model->file);
                $filename->filename = $model->file->name;
                $filename->save();
                $path = $filename->filename_id;
                $rows = $service->indexFile($model->file);
                $service->logUpload($rows, $path);
            }
            return $this->redirect(\Yii::$app->urlManager->createUrl("parsing/index"));
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionPlot()
    {
        return $this->render('plot');
    }

}