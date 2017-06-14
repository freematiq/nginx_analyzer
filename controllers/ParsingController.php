<?php

namespace app\controllers;

use app\models\Logs;
use app\models\PlotReference;
use app\models\UploadHistory;
use app\services\LogParserService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\PlotCreation;

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
        $plotCreation = new PlotCreation();
        $plotCreation->load(Yii::$app->request->get()) && $plotCreation->validate();
        if (empty($plotCreation->interval_quantity) &&
            empty($plotCreation->date_from &&
            empty($plotCreation->date_to))) {
            $plotCreation->interval_quantity = 60;
            $plotCreation->date_from = '2017-02-21 22:15:00';
            $plotCreation->date_to = '2017-02-21 22:25:00';
        };
        if (is_numeric($plotCreation->interval_quantity)===false
        ){
            throw new \Exception('Введите число');
        }
        $plot1 = $plotCreation->creation();
        $plot2 = $plotCreation->average();
        $plot3 = $plotCreation->groupbysip();
        $plot4 = $plotCreation->groupbyurl();
        $plot5 = $plotCreation->groupbycode();
        $plot6 = $plotCreation->groupbytime();

        return $this->render('plot', [
            'plotCreation' => $plotCreation,
            'plot1' => $plot1,
            'plot2' => $plot2,
            'plot3' => $plot3,
            'plot4' => $plot4,
            'plot5' => $plot5,
            'plot6' => $plot6,
        ]);
    }

    public function actionPlotfromhistory()
    {
        $plotCreation = new PlotReference();
        $plotCreation->load(Yii::$app->request->get());
        $plot1 = $plotCreation->plotfromfile();
        return $this->render('plotfromhistory', [
            'plotCreation' => $plotCreation,
            'plot1' => $plot1]);
    }

}