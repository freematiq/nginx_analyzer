<?php

use yii\db\Migration;

/**
 * Handles adding user_ip_reserve to table `logs`.
 */
class m170602_093345_add_user_ip_reserve_column_to_logs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('logs', 'user_ip_reserve', 'cidr');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('logs', 'user_ip_reserve');
    }
}
