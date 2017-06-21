<?php

/**
 * @var array $plot1 app\controllers\ParsingController
 * @var array $plot2 app\controllers\ParsingController
 * @var array $plot3 app\controllers\ParsingController
 * @var array $plot4 app\controllers\ParsingController
 * @var array $plot5 app\controllers\ParsingController
 * @var array $plot6 app\controllers\ParsingController
 * @var array $plot7 app\controllers\ParsingController
 * @var app\models\PlotCreation $plotCreation
 */

use kartik\daterange\DateRangePicker;
use yii\data\SqlDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii2mod\c3\chart\Chart;

$this->title = 'Plots';
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Графики</h1>
    </div>

    <?php

    $form = ActiveForm::begin([
        'id' => 'plotCreation',
        'method' => 'get',
        'action' => Url::to(['parsing/plot']),
    ]) ?>

    <?=
    $form->field($plotCreation, 'some_attribute')->widget(DateRangePicker::className(), [
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
    <?= $form->field($plotCreation, 'interval_quantity')->label('Шаг разбиения (60 = 1 минута, 3600 = 1 час, 86400 = 1 день)') ?>

    <div class="form-group">
        <?= Html::submitButton('Показать', ['class' => 'btn btn-info']) ?>
    </div>
    <?php ActiveForm::end();

    $interval = ArrayHelper::getColumn($plot1, 'interval');
    $quantity = ArrayHelper::getColumn($plot1, 'quantity');
    $arrX = array_merge(['x'], $interval);
    $interval2 = ArrayHelper::getColumn($plot2, 'interval');
    $arrY = array_merge(['количество запросов в момент времени'], $quantity);
    $quantity2 = ArrayHelper::getColumn($plot2, 'quantity');
    $arrX2 = array_merge(['x'], $interval2);
    $arrY2 = array_merge(['среднее время выполнения запроса'], $quantity2);
    $queries = ArrayHelper::getColumn($plot3, 'queries');
    $sip = ArrayHelper::getColumn($plot3, 'user_ip');
    $arrX3 = array_merge(['x1'], $sip);
    $arrY3 = array_merge(['количество запросов с ip'], $queries);
    $queries2 = ArrayHelper::getColumn($plot4, 'queries');
    $url_query = ArrayHelper::getColumn($plot4, 'url_query');
    $arrX4 = array_merge(['x2'], $url_query);
    $arrY4 = array_merge(['количество запросов с url'], $queries2);
    $queries3 = ArrayHelper::getColumn($plot5, 'queries');
    $query_code = ArrayHelper::getColumn($plot5, 'query_code');
    $arrX5 = array_merge(['x3'], $query_code);
    $arrY5 = array_merge(['количество запросов с кодом'], $queries3);
    $queries4 = ArrayHelper::getColumn($plot6, 'queries');
    $url_query2 = ArrayHelper::getColumn($plot6, 'url_query');
    $arrX6 = array_merge(['x4'], $url_query2);
    $arrY6 = array_merge(['общее время запросов с url'], $queries4);

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
                'xFormat' => '%Y',
                'columns' => [$arrX, $arrY, $arrX2, $arrY2,],
                'colors' => ['среднее время выполнения запроса' => '#385e3e', 'количество запросов в момент времени' => '#513535'],
                'type' => 'area-spline',
                'label' => true,
            ],
                'axis' => [
                    'x' => [
                        'show' => false,
                        'label' => 'Промежутки времени',
                        'type' => 'category',
                        'localtime' => false,
                        'tick' => [['format' => '%Y-%m-%d %H:%M:%S.%'],],
                    ],
                    'y' => [
                        'label' => ['text' => 'Количество запросов || секунды', 'position' => 'outer-middle'],
                    ],
                ]
            ]
    ]);

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
                        'tick' => ['width' => 120],
                        'show' => true,
                        'type' => 'category'
                        ,],
                    'y' => [
                        'label' => ['text' => 'Количество запросов', 'position' => 'outer-middle'],
                    ]
                ]
            ]
    ]);
    ?>

    <div class="container container-table">
        <h2>
            <p class="text-center">Количество запросов с определеным кодом запроса</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'code'],
        'clientOptions' =>
            ['data' => [
                'x' => 'x3',
                'columns' => [$arrX5, $arrY5],
                'colors' => ['количество запросов с кодом' => '#8e0b2c'],
                'type' => 'bar',
                'hide' => false,
            ],
                'axis' => [
                    'rotated' => true,
                    'x' => [
                        'show' => true,
                        'type' => 'category',
                        'label' => ['text' => 'Код запроса', 'position' => 'outer-middle']
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
            <p class="text-center">Top 20 url, которые чаще всего обрабатываются</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'url'],
        'clientOptions' =>
            ['size' => ['height' => 400],
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
                        'tick' => ['width' => 270],
                        'show' => true,
                        'type' => 'category',
                        'label' => ['text' => 'URL', 'position' => 'outer-middle']
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
            <p class="text-center">Общее время выполнения запросов с url в сек.(TOP20)</p>
        </h2>
    </div>

    <?php
    echo Chart::widget([
        'options' => ['id' => 'time'],
        'clientOptions' =>
            ['size' => ['height' => 400],
                'data' => [
                    'x' => 'x4',
                    'columns' => [$arrX6, $arrY6],
                    'colors' => ['общее время запросов с url' => '#8e0b2c'],
                    'type' => 'bar',
                    'hide' => false,
                ],
                'axis' => [
                    'rotated' => true,
                    'x' => [
                        'tick' => ['width' => 270],
                        'show' => true,
                        'type' => 'category',
                        'label' => ['text' => 'URL', 'position' => 'outer-middle']
                    ],
                    'y' => [
                        'label' => ['text' => 'Секунды', 'position' => 'outer-middle'],
                    ]
                ]
            ]
    ]);

    $total = Yii::$app->db->createCommand('
                      SELECT count(*) 
                      FROM (
                            SELECT count(url_query) 
                            FROM logs 
                            WHERE query_date BETWEEN :date_from AND :date_to AND query_code!=200
                            GROUP BY url_query
                            ) AS count', [
        'date_from' => $plotCreation->date_from,
        'date_to' => $plotCreation->date_to
    ])
        ->queryScalar();

    $url_provider = new SqlDataProvider([
        'sql' => 'SELECT query_code Код_запроса, 
                         url_query Адрес_запроса, 
                         count(query_code) Количество  
                      FROM logs
                      WHERE query_date BETWEEN :date_from AND :date_to AND query_code!=200
                      GROUP BY url_query, query_code 
                      ORDER BY Код_запроса DESC, Количество DESC',
        'params' => [':date_from' => $plotCreation->date_from, ':date_to' => $plotCreation->date_to],
        'totalCount' => $total,
        'pagination' => ['pageSize' => 100],
    ]);

    $provider = new SqlDataProvider([
        'sql' => $plot7,
        'params' => [':date_from' => $plotCreation->date_from, ':date_to' => $plotCreation->date_to],
        'totalCount' => 20,
    ]);
    ?>
    <div class="container container-table">
        <h2>
            <p class="text-center">Время выполнения запросов в сек.</p>
        </h2>
    </div>

    <?php
    echo GridView::widget([
        'summary' => false,
        'dataProvider' => $provider,
        'captionOptions' => ['class' => 'h4 text-center text-info'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'url_query',
                'label' => 'URL',
                'format' => 'raw',],
            ['attribute' => 'Максимальное_время',
                'label' => 'Максимальное время',
                'format' => 'raw',],
            ['attribute' => 'Среднее_время',
                'label' => 'Среднее время',
                'format' => 'raw',],
            ['attribute' => 'Минимальное_время',
                'label' => 'Минимальное время',
                'format' => 'raw',],
            ['attribute' => 'Время_максимального_запроса',
                'label' => 'Время максимального запроса',
                'format' => 'raw',],
            ['attribute' => 'Время_минимального_запроса',
                'label' => 'Время минимального запроса',
                'format' => 'raw',],
        ],
    ]);

    ?>

    <div class="container container-table">
        <h2>
            <p class="text-center">Количество кодов запросов с url (все, кроме 200)</p>
        </h2>
    </div>

    <?php
    Pjax::begin(['timeout' => 8000]);

    echo GridView::widget([
        'summary' => false,
        'dataProvider' => $url_provider,
        'captionOptions' => ['class' => 'h4 text-center text-info'],
        'emptyText' => 'Все с кодом 200',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'Код_запроса',
                'label' => 'Код запроса',
                'format' => 'raw',],
            ['attribute' => 'Адрес_запроса',
                'label' => 'Адрес запроса',
                'format' => 'raw',],
            ['attribute' => 'Количество',
                'label' => 'Количество',
                'format' => 'raw',],
        ],
    ]);
    Pjax::end();
    ?>

</div>
