<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $log_id
 * @property string $sip
 * @property string $query_date
 * @property integer $query_type
 * @property string $url_query
 * @property integer $query_code
 * @property integer $query_size
 * @property string $query_time
 * @property string $quested_page
 * @property integer $browser_info
 * @property string $user_ip
 * @property integer $uploaded_file
 * @property string $created_at
 *
 * @property QueryTypes $queryType
 * @property UploadHistory $uploadedFile
 * @property UserAgents $browserInfo
 */
class Logs extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'logs';
    }

    public function rules()
    {
        return [
            [['sip', 'user_ip'], 'string'],
            [['query_date', 'query_time', 'created_at'], 'safe'],
            [['query_type', 'query_code', 'query_size', 'browser_info', 'uploaded_file'], 'integer'],
            [['url_query'], 'string', 'max' => 128],
            [['quested_page'], 'string', 'max' => 256],
            [['query_type'], 'exist', 'skipOnError' => true, 'targetClass' => QueryTypes::className(), 'targetAttribute' => ['query_type' => 'id']],
            [['uploaded_file'], 'exist', 'skipOnError' => true, 'targetClass' => UploadHistory::className(), 'targetAttribute' => ['uploaded_file' => 'id']],
            [['browser_info'], 'exist', 'skipOnError' => true, 'targetClass' => UserAgents::className(), 'targetAttribute' => ['browser_info' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'sip' => 'Sip',
            'query_date' => 'Query Date',
            'query_type' => 'Query Type',
            'url_query' => 'Url Query',
            'query_code' => 'Query Code',
            'query_size' => 'Query Size',
            'query_time' => 'Query Time',
            'quested_page' => 'Quested Page',
            'browser_info' => 'Browser Info',
            'user_ip' => 'User Ip',
            'uploaded_file' => 'Uploaded File',
            'created_at' => 'Created At',
        ];
    }

    public function getQueryType()
    {
        return $this->hasOne(QueryTypes::className(), ['id' => 'query_type']);
    }

    public function getUploadedFile()
    {
        return $this->hasOne(UploadHistory::className(), ['id' => 'uploaded_file']);
    }

    public function getBrowserInfo()
    {
        return $this->hasOne(UserAgents::className(), ['id' => 'browser_info']);
    }
}
