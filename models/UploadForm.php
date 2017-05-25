<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model{


    public function rules()
    {
        return [
            [['file'], 'file'],
        ];
    }
}
