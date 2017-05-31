<?php

namespace app\controllers;

use app\models\Logs;
use app\models\UploadHistory;
use app\services\LogParserService;
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
        $parser = new LogParserService();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $model->file->saveAs($model->file);
                $filename->filename = $model->file->name;
                $filename->save();
                $filename_id = $filename->filename_id;
                $rows = $parser->fileToParse($model->file);
                $parser->logUploadThroughBrowser($rows, $filename_id);
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