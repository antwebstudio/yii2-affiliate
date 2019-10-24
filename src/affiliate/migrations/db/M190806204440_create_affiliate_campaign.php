<?php

namespace ant\affiliate\migrations\db;

use ant\components\Migration;

/**
 * Class M190806204440_create_affiliate_Campaign
 */
class M190806204440_create_affiliate_campaign extends Migration
{
    protected $tableName = '{{%affiliate_campaign}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'model_class_id' => $this->integer()->unsigned(),
            'model_id' => $this->integer()->unsigned(),
            'commission_percent' => $this->integer()->null()->defaultValue(null),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%model_class}}', 'model_class_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "M190806204440_create_affiliate_Campaign cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M190806204440_create_affiliate_Campaign cannot be reverted.\n";

        return false;
    }
    */
}
