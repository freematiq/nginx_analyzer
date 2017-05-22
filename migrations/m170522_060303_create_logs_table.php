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
            'query_time' => 'time',
            'quested_page' => 'varchar(256)',
            'browser_info' => 'integer',
            'user_ip' => 'cidr',
            'uploaded_file' => 'integer',
            'created_at' => 'timestamp(0) with time zone default now() not null',
        ]);

        $this->addForeignKey(
            'fk-browser_info',
            'logs',
            'browser_info',
            'user_agents',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-query_type',
            'logs',
            'query_type',
            'query_types',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk-uploaded_history',
            'logs',
            'uploaded_file',
            'upload_history',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('logs');

        $this->dropForeignKey(
            'fk-query_type',
            'logs'
        );

        $this->dropForeignKey(
            'fk-browser_info',
            'logs'
        );

        $this->dropForeignKey(
            'fk-uploaded_history',
            'logs'
        );
    }
}
