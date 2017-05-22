<?php

use yii\db\Migration;

/**
 * Handles the creation of table `useragents`.
 */
class m170522_045633_create_useragents_table extends Migration
{

    public function up()
    {
        $this->createTable('useragents', [
            'id' => $this->primaryKey(),
            'browserinfo' => 'varchar(256)',
        ]);
    }

    public function down()
    {
        $this->dropTable('useragents');
    }
}
