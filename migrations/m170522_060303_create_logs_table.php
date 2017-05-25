<?php

use yii\db\Migration;

class m170522_060303_create_logs_table extends Migration
{

    public function up()
    {
        $this->createTable('logs', [
            'log_id' => $this->primaryKey(),
            'sip' => 'cidr',
            'query_date' => 'timestamp with time zone',
            'query_type' => 'integer',
            'url_query' => 'varchar(128)',
            'query_code' => 'integer',
            'query_size' => 'integer',
            'query_time_numeric' => 'NUMERIC(19,3)',
            'query_time_float' => 'float',
            'quested_page' => 'varchar(256)',
            'browser_info' => 'integer',
            'user_ip' => 'cidr',
            'uploaded_file' => 'integer',
            'created_at' => 'timestamp(0) with time zone default now() not null',
        ]);

        $this->addForeignKey(
            'fk_browser_info',
            'logs',
            'browser_info',
            'user_agents',
            'user_agent_id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_query_type',
            'logs',
            'query_type',
            'query_types',
            'query_type_id',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk_uploaded_history',
            'logs',
            'uploaded_file',
            'upload_history',
            'filename_id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('logs');

        $this->dropForeignKey(
            'fk_query_type',
            'logs'
        );

        $this->dropForeignKey(
            'fk_browser_info',
            'logs'
        );

        $this->dropForeignKey(
            'fk_uploaded_history',
            'logs'
        );
    }
}
