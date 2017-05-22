<?php

use yii\db\Migration;

/**
 * Handles the creation of table `uploadhistory`.
 */
class m170522_050221_create_uploadhistory_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('uploadhistory', [
            'id' => $this->primaryKey(),
            'filename' => 'char',
            'date' => 'timestamp(0) with time zone default now() not null',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('uploadhistory');
    }
}
