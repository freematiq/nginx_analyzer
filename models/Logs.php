<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\db\QueryBuilder;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

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
 * @property string $quested_page
 * @property integer $browser_info
 * @property string $user_ip
 * @property integer $uploaded_file
 * @property string $created_at
 * @property double $query_time_float
 * @property string $query_time_numeric
 *
 * @property QueryTypes $queryType
 * @property UploadHistory $uploadedFile
 * @property UserAgents $browserInfo
 */
class Logs extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sip', 'user_ip'], 'string'],
            [['query_date', 'created_at'], 'safe'],
            [['query_type', 'query_code', 'query_size', 'browser_info', 'uploaded_file'], 'integer'],
            [['query_time_float', 'query_time_numeric'], 'number'],
            [['url_query'], 'string', 'max' => 128],
            [['quested_page'], 'string', 'max' => 256],
            [['query_type'], 'exist', 'skipOnError' => true, 'targetClass' => QueryTypes::className(), 'targetAttribute' => ['query_type' => 'query_type_id']],
            [['uploaded_file'], 'exist', 'skipOnError' => true, 'targetClass' => UploadHistory::className(), 'targetAttribute' => ['uploaded_file' => 'filename_id']],
            [['browser_info'], 'exist', 'skipOnError' => true, 'targetClass' => UserAgents::className(), 'targetAttribute' => ['browser_info' => 'user_agent_id']],
            [['file'], 'file'],
        ];
    }

    /**
     * @inheritdoc
     */
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
            'quested_page' => 'Quested Page',
            'browser_info' => 'Browser Info',
            'user_ip' => 'User Ip',
            'uploaded_file' => 'Uploaded File',
            'created_at' => 'Created At',
            'query_time_float' => 'Query Time Float',
            'query_time_numeric' => 'Query Time Numeric',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQueryType()
    {
        return $this->hasOne(QueryTypes::className(), ['query_type_id' => 'query_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedFile()
    {
        return $this->hasOne(UploadHistory::className(), ['filename_id' => 'uploaded_file']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBrowserInfo()
    {
        return $this->hasOne(UserAgents::className(), ['user_agent_id' => 'browser_info']);
    }

}
