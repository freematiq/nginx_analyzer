<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_agents".
 *
 * @property integer $id
 * @property string $browser_info
 *
 * @property Logs[] $logs
 */
class UserAgents extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'user_agents';
    }

    public function rules()
    {
        return [
            [['browser_info'], 'string', 'max' => 256],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'browser_info' => 'Browser Info',
        ];
    }

    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['browser_info' => 'id']);
    }
}
