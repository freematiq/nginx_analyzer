<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_agents".
 *
 * @property integer $user_agent_id
 * @property string $browser_info
 *
 * @property Logs[] $logs
 */
class UserAgents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_agents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['browser_info'], 'string', 'max' => 256],
            [['browser_info'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_agent_id' => 'User Agent ID',
            'browser_info' => 'Browser Info',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['browser_info' => 'user_agent_id']);
    }
}
