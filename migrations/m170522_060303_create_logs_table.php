<?php

use yii\db\Migration;

class m170522_060303_create_logs_table extends Migration
{

    public function up()
    {
        $this->createTable('nlogs', [
            'logid' => $this->primaryKey(),
            'sip' => 'cidr',
            'querydate' => 'timestamp with time zone',
            'querytype' => 'integer',
            'urlquery' => 'char',
            'querycode' => 'integer',
            'querysize' => 'integer',
            'querytime' => $this->time(),
            'questedpage' => 'varchar(256)',
            'browserinfo' => 'integer',
            'userip' => 'cidr',
            'uploadedfile' => 'integer',
            'created_at' => 'timestamp(0) with time zone default now() not null',
        ]);

        $this->addForeignKey(
            'fk-browserinfo',
            'nlogs',
            'browserinfo',
            'useragents',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-querytype',
            'nlogs',
            'querytype',
            'querytypes',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk-uploadedhistory',
            'nlogs',
            'uploadedfile',
            'uploadhistory',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('logs');

        $this->dropForeignKey(
            'fk-querytype',
            'nlogs'
        );

        $this->dropForeignKey(
            'fk-browserinfo',
            'nlogs'
        );

        $this->dropForeignKey(
            'fk-uploadedhistory',
            'nlogs'
        );
    }
}
