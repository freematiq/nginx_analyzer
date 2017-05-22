<?php

use yii\db\Migration;

/**
 * Handles the creation of table `useragents`.
 */
class m170522_045633_create_useragents_table extends Migration
{

    public function up()
    {
        $this->createTable('user_agents', [
            'user_agent_id' => $this->primaryKey(),
            'browser_info' => 'varchar(256)',
        ]);

        $this->createIndex('index_unique_user_agents',
            'user_agents',
            'browser_info',
            true);
    }

    public function down()
    {
        $this->dropTable('user_agents');

        $this->dropIndex('index_unique_user_agents',
            'user_agents');
    }
}
