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
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii2mod\c3\chart\Chart;

$this->title = 'Start';
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Графики</h1>
    </div>

    <?php

    $form = ActiveForm::begin([
        'id' => 'model',
        'method' => 'get',
        'action' => Url::to(['parsing/plot']),
    ]) ?>

    <?= $form->field($model, 'some_attribute')->widget(DateRangePicker::className(), [
        'name' => 'date_range',
        'convertFormat' => true,
        'startAttribute' => 'date_from',
        'endAttribute' => 'date_to',
        'pluginOptions' => [
            'timePicker24Hour' => true,
            'timePicker' => true,
            'timePickerIncrement' => 5,
            'locale' => ['format' => 'Y-m-d H:i:s']
        ]
    ])->label('Период времени') ?>
    <?= $form->field($model, 'interval_quantity')->label('Шаг разбиения (60 = 1 минута, 3600 = 1 час, 86400 = 1 день)') ?>

    <div class="form-group">
        <?= Html::submitButton('Показать', ['class' => 'btn btn-info']) ?>
    </div>
    <?php ActiveForm::end();

    $interval = ArrayHelper::getColumn($data, 'interval');
    $quantity = ArrayHelper::getColumn($data, 'quantity');
    $arrX = array_merge(['x'], $interval);
    $arrY = array_merge(['количество запросов в момент времени'], $quantity);

    ?>

    <?php
    /* echo Chart::widget([

         'options' => ['id' => 'count'],
         'clientOptions' =>
             ['data' => [
                 'x' => 'x',
                 'columns' => [$arrX, $arrY,],
                 'colors' => ['количество запросов в момент времени' => '#513535'],
                 'type' => 'area-spline',
                 'labels' => true,
             ],
                 'axis' => [
                     'x' => [
                         'show' => false,
                         'label' => 'Промежутки времени',
                         'type' => 'category',
                         'tick' => [['format' => '%Y-%m-%d'],],
                     ],
                     'y' => [
                         'label' => ['text' => 'Запросы', 'position' => 'outer-middle'],
                     ]
                 ]
             ]
     ]);*/

    $interval2 = ArrayHelper::getColumn($data2, 'interval');
    $quantity2 = ArrayHelper::getColumn($data2, 'quantity');
    $arrX2 = array_merge(['x'], $interval2);
    $arrY2 = array_merge(['среднее время выполнения запроса'], $quantity2);

    ?>
    <div class="container container-table">
        <h2>
            <p class="text-center">Количество запросов и их среднее время выполнения в момент времени</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'average'],
        'clientOptions' =>
            ['data' => [
                'x' => 'x',
                'columns' => [$arrX, $arrY, $arrX2, $arrY2,],
                'colors' => ['среднее время выполнения запроса' => '#385e3e'],
                'type' => 'area-spline',
                'labels' => true,
            ],
                'axis' => [
                    'y2' => [
                        'show' => true,
                        'label' => ['text' => 'Время выполнения (сек)', 'position' => 'outer-middle'],
                    ],
                    'x' => [
                        'show' => false,
                        'label' => 'Промежутки времени',
                        'type' => 'category',
                        'tick' => [['format' => '%Y-%m-%d'],],
                    ],
                    'y' => [
                        'label' => ['text' => 'Количество запросов', 'position' => 'outer-middle'],
                    ]
                ]
            ]
    ]);

    $queries = ArrayHelper::getColumn($data3, 'queries');
    $sip = ArrayHelper::getColumn($data3, 'sip');
    $arrX3 = array_merge(['x1'], $sip);
    $arrY3 = array_merge(['количество запросов с ip'], $queries);
    $queries2 = ArrayHelper::getColumn($data4, 'queries');
    $url_query = ArrayHelper::getColumn($data4, 'url_query');
    $arrX4 = array_merge(['x2'], $url_query);
    $arrY4 = array_merge(['количество запросов с url'], $queries2);

    ?>
    <div class="container container-table">
        <h2>
            <p class="text-center">Top 20 ip, с которых было больше всего запросов</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'sip'],
        'clientOptions' =>
            ['data' => [
                'x' => 'x1',
                'columns' => [$arrX3, $arrY3],
                'colors' => ['количество запросов с ip' => '#8e0b2c',
                    'количество запросов с url' => '#8c7379'],
                'type' => 'bar',
                'hide' => false,
            ],
                'axis' => [
                    'rotated' => true,
                    'x' => [
                        'show' => true,
                        'type' => 'category',
                    ],
                    'y' => [
                        'label' => ['text' => 'Количество запросов', 'position' => 'outer-middle'],
                    ]
                ]
            ]
    ]);

    ?>
    <div class="container container-table">
        <h2>
            <p class="text-center">Top 20 url, с которых было больше всего запросов</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'url'],
        'clientOptions' =>
            ['size' => ['height' => 730],
                'data' => [
                    'x' => 'x2',
                    'columns' => [$arrX4, $arrY4],
                    'colors' => ['количество запросов с ip' => '#8e0b2c',
                        'количество запросов с url' => '#8c7379'],
                    'type' => 'bar',
                    'hide' => false,
                ],
                'axis' => [
                    'rotated' => true,
                    'x' => [
                        'show' => true,
                        'type' => 'category',
                    ],
                    'y' => [
                        'label' => ['text' => 'Количество запросов', 'position' => 'outer-middle'],
                    ]
                ]
            ]
    ]);

    $provider = new SqlDataProvider([
        'sql' => 'SELECT url_query URL, 
                             query_time_numeric Время_выполнения, 
                             query_date Дата_запроса 
                      FROM logs 
                      ORDER BY query_time_numeric DESC',
        'totalCount' => 20]);
    ?>
     <div class="container container-table">
         <h2>
             <p class="text-center">Top 20 самых долгих запросов</p>
         </h2>
     </div>

     <?php
        echo GridView::widget([
            'summary' => false,
            'dataProvider' => $provider,
            'captionOptions' => ['class'=>'h4 text-center text-info'],
        ]);

    ?>
</div>
