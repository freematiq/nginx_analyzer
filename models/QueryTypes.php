<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "query_types".
 *
 * @property integer $query_type_id
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
            [['query_type'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'query_type_id' => 'Query Type ID',
            'query_type' => 'Query Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Logs::className(), ['query_type' => 'query_type_id']);
    }
}
