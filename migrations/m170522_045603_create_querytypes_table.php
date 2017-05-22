<?php

use yii\db\Migration;

/**
 * Handles the creation of table `querytypes`.
 */
class m170522_045603_create_querytypes_table extends Migration
{

    public function up()
    {
        $this->createTable('query_types', [
            'id' => $this->primaryKey(),
            'query_type' => 'char(10)',
        ]);
    }

    public function down()
    {
        $this->dropTable('query_types');
    }
}
