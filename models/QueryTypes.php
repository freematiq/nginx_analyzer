<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "query_types".
 *
 * @property integer $id
 * @property string $query_type
 *
 * @property Logs[] $logs
 */
class QueryTypes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'query_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['query_type'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'query_type' => 'Query Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['query_type' => 'id']);
    }
}
