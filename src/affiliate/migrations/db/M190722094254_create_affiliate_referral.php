<?php

namespace ant\affiliate\migrations\db;

use common\components\Migration;

/**
 * Class M190722094254_create_affiliate_referral
 */
class M190722094254_create_affiliate_referral extends Migration
{
    protected $tableName = '{{%affiliate_referral}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull()->unique(),
            'user_id' => $this->integer()->unsigned()->null()->defaultValue(null),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'created_at' => $this->timestamp()->null()->defaultValue(null),
            'updated_at' => $this->timestamp()->null()->defaultValue(null),
        ], $this->getTableOptions());

        $this->addForeignKeyTo('{{%user}}', 'user_id', self::FK_TYPE_SET_NULL, self::FK_TYPE_SET_NULL);
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
        echo "M190722094254_create_affiliate_referral cannot be reverted.\n";

        return false;
    }
    */
}
