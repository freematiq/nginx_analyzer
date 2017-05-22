<?php

use yii\db\Migration;

/**
 * Handles the creation of table `querytypes`.
 */
class m170522_045603_create_querytypes_table extends Migration
{

    public function up()
    {
        $this->createTable('querytypes', [
            'id' => $this->primaryKey(),
            'querytype' => 'char',
        ]);
    }

    public function down()
    {
        $this->dropTable('querytypes');
    }
}
