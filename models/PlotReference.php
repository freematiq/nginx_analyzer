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

    public function plotfromfile()
    {
        $plot = Yii::$app->db->createCommand(
            'SELECT min(query_date) a, 
                        max(query_date) b
                FROM logs 
                WHERE uploaded_file= :filename_id', [
            'filename_id' => $this->filename_id,
        ])->queryAll();

        return $plot;
    }

}