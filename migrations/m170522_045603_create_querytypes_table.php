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
            'query_type_id' => $this->primaryKey(),
            'query_type' => 'char(10)',
        ]);

        $this->createIndex('index_unique_query_types',
            'query_types',
            'query_type',
            true);
    }

    public function down()
    {
        $this->dropTable('query_types');

        $this->dropIndex('index_unique_query_types',
            'query_types');
    }
}
