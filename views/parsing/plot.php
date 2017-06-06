<?php

/**
 * @var $data app\controllers\ParsingController
 * @var $model app\controllers\ParsingController
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

    <?= $form->field($model, 'some_attribute')->widget(DateRangePicker::className(),[
        'name' => 'date_range',
        'convertFormat' => true,
        'startAttribute' => 'date_from',
        'endAttribute' => 'date_to',
        'startInputOptions' => ['value' => '2017-02-01 12:00:00'],
        'endInputOptions' => ['value' => '2017-04-02 11:00:00'],
        'pluginOptions' => [
            'timePicker' => true,
            'timePickerIncrement' => 10,
            'locale' => ['format' => 'Y-m-d h:i:s']
        ]
    ])->label('Период времени') ?>
    <?= $form->field($model, 'interval_quantity')->label('Количество секунд для группировки') ?>

    <div class="form-group">
        <?= Html::submitButton('Показать', ['class' => 'btn btn-info']) ?>
    </div>
    <?php ActiveForm::end();

  //  var_dump($data);
    $interval = ArrayHelper::getColumn($data, 'interval');
    //var_dump($interval);
    $quantity = ArrayHelper::getColumn($data, 'quantity');
    //var_dump($quantity);

    $arrX = array_merge(['x'], $interval);
    $arrY = array_merge(['количество запросов'], $quantity);
    echo Chart::widget([
        'options' => [
            'id' => 'timeseries_chart'
        ],
        'clientOptions' => [
            'data' => [
                'x' => 'x',
                'columns' => [
                    $arrX,
                    $arrY,
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

    ?>
</div>
