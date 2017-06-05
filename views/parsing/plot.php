<?php

/**
 * @var $data app\controllers\ParsingController
 */


use kartik\daterange\DateRangePicker;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\SqlDataProvider;
use yii\db\ActiveQuery;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii2mod\c3\chart\Chart;

$this->title = 'Start';
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Графики</h1>
    </div>

    <?php

    $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'date_from')->label('Дата от времени') ?>
    <?= $form->field($model, 'date_to')->label('Дата до времени') ?>
    <?= $form->field($model, 'interval_quantity')->label('Количество разбиений') ?>

    <div class="form-group">
        <?= Html::submitButton('Показать', ['class' => 'btn btn-info']) ?>
    </div>
    <?php ActiveForm::end();
    var_dump($data);

    $interval = ArrayHelper::getColumn($data,'interval');
    var_dump($interval);
    $quantity = ArrayHelper::getColumn($data,'quantity');
    var_dump($quantity);

    /*    echo DateRangePicker::widget([
        'name' => 'date_range',
        'value' => '2017-05-02 01:00:00 PM - 2017-05-03 01:00:00 PM',
        'convertFormat' => true,
        'startAttribute' => 'date_from',
        'endAttribute' => 'date_to',
        'pluginOptions' => [
            'timePicker' => true,
            'timePickerIncrement' => 10,
            'locale' => ['format' => 'Y-m-d h:i:s']
        ]
    ]);*/

    $a = [1,2,3];
    $b = [10,20,30];
    echo Chart::widget([
        'options' => [
            'id' => 'timeseries_chart'
        ],
        'clientOptions' => [
            'data' => [
                'x' => 'x',
                'columns' => [

                    ['x', $a],
                    ['y', 1,2,3],
                ],
            ],
            'axis' => [
                'x' => [
                    'label' => 'Timeline',
                    'type' => 'category',
                    'tick' => [
                        'format' => '%Y-%m-%d'
                    ],
                ],
            ]
        ]
    ]);

    /*$from_date = '2017-05-22 09:00:00+07';
    $to_date = '2017-05-25 10:00:00+07';

    $rowed = (new \yii\db\Query())
        ->select(['diff' => '(max(query_date)-min(query_date))/24'])
        ->from('logs')
        ->all();

    $rows = (new \yii\db\Query())
        ->select(['date' => 'query_date', 'count' => 'count(*)'])
        ->from('logs')
        ->where(['between', 'query_date', $from_date, $to_date])
        ->groupBy('query_date')
        ->orderBy('query_date')
        ->all();

    //var_dump($rows);
    var_dump($rowed);*/
    ?>
</div>
