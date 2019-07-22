<?php

namespace ant\affiliate\migrations\db;

use common\components\Migration;

/**
 * Class M190722095055_create_affiliate_referral_contribution
 */
class M190722095055_create_affiliate_referral_contribution extends Migration
{
    protected $tableName = '{{%affiliate_referral_contribution}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'referral_id' => $this->integer(10)->unsigned(),
            'order_id' => $this->integer(10)->unsigned(),
            'commission_amount' => $this->money(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%affiliate_referral}}', 'referral_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
        $this->addForeignKeyTo('{{%order}}', 'order_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190722095055_create_affiliate_referral_contribution cannot be reverted.\n";

        return false;
    }
    */
}
