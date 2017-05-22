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
        $this->createTable('upload_history', [
            'filename_id' => $this->primaryKey(),
            'filename' => 'varchar(256)',
            'date' => 'timestamp(0) with time zone default now() not null',
        ]);

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('upload_history');
    }
}
