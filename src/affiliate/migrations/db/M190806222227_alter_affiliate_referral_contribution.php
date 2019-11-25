<?php

namespace ant\affiliate\migrations\db;

use ant\db\Migration;

/**
 * Class M190806222227_alter_affiliate_referral_contribution
 */
class M190806222227_alter_affiliate_referral_contribution extends Migration
{
    protected $tableName = '{{%affiliate_referral_contribution}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKeyTo('{{%affiliate_referral}}', 'referral_id');
        $this->addForeignKeyTo('{{%affiliate_referral}}', 'referral_id', self::FK_TYPE_RESTRICT, self::FK_TYPE_RESTRICT);
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKeyTo('{{%affiliate_referral}}', 'referral_id');
        $this->addForeignKeyTo('{{%affiliate_referral}}', 'referral_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
        
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190806222227_alter_affiliate_referral_contribution cannot be reverted.\n";

        return false;
    }
    */
}
