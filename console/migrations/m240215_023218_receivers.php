<?php

use yii\db\Migration;

/**
 * Class m240215_023218_receivers
 */
class m240215_023218_receivers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%receivers}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'email_tg' => $this->string()->notNull(),
            'file' => $this->string(),
            'created_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240215_023218_receivers cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240215_023218_receivers cannot be reverted.\n";

        return false;
    }
    */
}
