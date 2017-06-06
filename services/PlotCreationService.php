<?php

namespace app\services;


use app\models\PlotCreation;
use Yii;
use yii\helpers\ArrayHelper;
use yii2mod\c3\chart\Chart;

class PlotCreationService
{
    public function creation()
    {
        $plot_model = new PlotCreation();
        $query_counter = Yii::$app->db->createCommand('SELECT COUNT(*) quantity, 
to_timestamp(floor((extract(\'epoch\' FROM query_date) / :quantity )) * :quantity) 
AT TIME ZONE \'UTC\' AS interval 
FROM logs 
WHERE query_date BETWEEN :date_from AND :date_to
GROUP BY INTERVAL ORDER BY INTERVAL',
            ['quantity' => $plot_model->interval_quantity,
                'date_from' => $plot_model->date_from,
                'date_to' => $plot_model->date_to])->queryAll();

        var_dump($query_counter);
        $interval = ArrayHelper::getColumn($query_counter, 'interval');
        $quantity = ArrayHelper::getColumn($query_counter, 'quantity');
        $arrX2 = array_merge(['x'], $interval);
        $arrY2 = array_merge(['общее число запросов'], $quantity);

        $average_query_size = Yii::$app->db->createCommand('SELECT AVG(query_time_numeric) average, 
to_timestamp(floor((extract(\'epoch\' FROM query_date) / :quantity )) * :quantity) 
AT TIME ZONE \'UTC\' AS interval 
FROM logs 
WHERE query_date BETWEEN :date_from AND :date_to
GROUP BY INTERVAL ORDER BY INTERVAL',
            ['quantity' => $plot_model->interval_quantity,
                'date_from' => $plot_model->date_from,
                'date_to' => $plot_model->date_to])->queryAll();

        var_dump($average_query_size);
        $interval2 = ArrayHelper::getColumn($average_query_size, 'interval');
        $average = ArrayHelper::getColumn($average_query_size, 'average');
        $arrX3 = array_merge(['x'], $interval2);
        $arrY3 = array_merge(['среднее время обработки запросов'], $average);

        return [$query_counter, $average_query_size];
    }

    /*public function average()
    {
        $plot = Yii::$app->db->createCommand('SELECT AVG(query_time_numeric) quantity,
to_timestamp(floor((extract(\'epoch\' FROM query_date) / :quantity )) * :quantity)
AT TIME ZONE \'UTC\' AS interval
FROM logs
WHERE query_date BETWEEN :date_from AND :date_to
GROUP BY INTERVAL ORDER BY INTERVAL',
            ['quantity' => $this->interval_quantity,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to])->queryAll();

        $interval2 = ArrayHelper::getColumn($plot, 'interval');
        $quantity2 = ArrayHelper::getColumn($plot, 'quantity');
        $arrX2 = array_merge(['x'], $interval2);
        $arrY2 = array_merge(['awf'], $quantity2);

        echo Chart::widget([
            'options' => [
                'id' => 'timeseries_chart'
            ],
            'clientOptions' => [
                'data' => [
                    'x' => 'x',
                    'columns' => [
                        $arrX2,
                        $arrY2,
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
        return $plot;
    }*/
}