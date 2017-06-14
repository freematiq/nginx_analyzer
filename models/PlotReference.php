<?php

namespace app\models;


use Yii;
use yii\base\Model;

class PlotReference extends Model
{
    public $filename_id;

    public function rules()
    {
        return [
            [['filename_id'], 'number'],
        ];
    }

}