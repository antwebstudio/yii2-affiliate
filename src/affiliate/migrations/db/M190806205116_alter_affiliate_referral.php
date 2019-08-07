<?php

namespace ant\affiliate\migrations\db;

use common\components\Migration;

/**
 * Class M190806205116_alter_affiliate_referral
 */
class M190806205116_alter_affiliate_referral extends Migration
{
    protected $tableName = '{{%affiliate_referral}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'campaign_id', $this->integer()->unsigned()->null()->defaultValue(null));
        $this->alterColumn($this->tableName, 'name', $this->string()->null()->defaultValue(null)->unique());

        $this->addForeignKeyTo('{{%affiliate_campaign}}', 'campaign_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKeyTo('{{%affiliate_campaign}}', 'campaign_id');
        
        $this->alterColumn($this->tableName, 'name', $this->string()->notNull()->unique());
        $this->dropColumn($this->tableName, 'campaign_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190806205116_alter_affiliate_referral cannot be reverted.\n";

        return false;
    }
    */
}
