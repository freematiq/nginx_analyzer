<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "upload_history".
 *
 * @property integer $id
 * @property string $filename
 * @property string $date
 *
 * @property Logs[] $logs
 */
class UploadHistory extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'upload_history';
    }

    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['filename'], 'string', 'max' => 256],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'date' => 'Date',
        ];
    }

    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['uploaded_file' => 'id']);
    }
}
