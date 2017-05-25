<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "upload_history".
 *
 * @property integer $filename_id
 * @property string $filename
 * @property string $date
 *
 * @property Logs[] $logs
 */
class UploadHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['filename'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filename_id' => 'Filename ID',
            'filename' => 'Filename',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['uploaded_file' => 'filename_id']);
    }

}
