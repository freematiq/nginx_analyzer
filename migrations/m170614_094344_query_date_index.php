<?php

use yii\db\Migration;

class m170614_094344_query_date_index extends Migration
{
    public function up()
    {
       $this->createIndex('query_date_index', 'logs', 'query_date', 'btree');
    }

    public function down()
    {
        $this->dropIndex('query_date_index', 'logs');
    }
}
