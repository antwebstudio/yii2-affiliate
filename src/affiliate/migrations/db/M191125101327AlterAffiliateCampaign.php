<?php

namespace ant\affiliate\migrations\db;

use ant\db\Migration;

/**
 * Class M191125101327AlterAffiliateCampaign
 */
class M191125101327AlterAffiliateCampaign extends Migration
{
    protected $tableName = '{{%affiliate_campaign}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'code', $this->string()->null()->defaultValue(null));
		$this->createUniqueIndexFor(['model_class_id', 'model_id', 'code']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'code');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M191125101327AlterAffiliateCampaign cannot be reverted.\n";

        return false;
    }
    */
}
