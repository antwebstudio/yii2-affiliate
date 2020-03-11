<?php

namespace ant\affiliate\migrations\db;

use yii\db\Migration;

/**
 * Class M200127152425AlterAffiliateReferralContribution
 */
class M200127152425AlterAffiliateReferralContribution extends Migration
{
    protected $tableName = '{{%affiliate_referral_contribution}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'user_id', $this->integer()->unsigned()->null()->defaultValue(null)->after('id'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'user_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M200127152425AlterAffiliateReferralContribution cannot be reverted.\n";

        return false;
    }
    */
}
