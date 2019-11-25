<?php

namespace ant\affiliate\migrations\db;

use ant\db\Migration;

/**
 * Class M191125080757CreateAffiliateCampaignContribution
 */
class M191125080757CreateAffiliateCampaignContribution extends Migration
{
    protected $tableName = '{{%affiliate_campaign_contribution}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'campaign_id' => $this->integer(10)->unsigned(),
            'order_id' => $this->integer(10)->unsigned(),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%affiliate_campaign}}', 'campaign_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
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
        echo "M191125080757CreateAffiliateCampaignContribution cannot be reverted.\n";

        return false;
    }
    */
}
