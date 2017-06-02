<?php

namespace app\models;

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

}

