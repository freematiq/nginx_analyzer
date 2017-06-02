<?php

namespace app\models;

use Yii;

class PlotCreation extends \yii\base\Model
{

    public $date_from;
    public $date_to;
    public $interval_quantity;

    public function rules()
    {
        return [
            [['interval_quantity'], 'number'],
            [['date_from', 'date_to', 'interval_quantity'], 'required'],
        ];
    }

    public function creation()
    {
        //$posts = new PlotCreation();
        $posts = Yii::$app->db->createCommand('SELECT COUNT(*) quantity, 
to_timestamp(floor((extract(\'epoch\' FROM query_date) / :quantity )) * :quantity) 
AT TIME ZONE \'UTC\' AS interval 
FROM logs 
WHERE query_date BETWEEN :date_from AND :date_to
GROUP BY INTERVAL ORDER BY INTERVAL',
            ['quantity' => $this->interval_quantity,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to])->queryAll();
        return $posts;
    }

}

