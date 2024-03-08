<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%position}}`.
 */
class m240307_004157_create_position_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


        $this->createTable('{{%position}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'role' => $this->string(),
            // Add other fields as needed
        ]);

        $this->createTable('{{%employees}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'surname' => $this->string()->notNull(),
            'photo' => $this->string(),
            'date_of_birth' => $this->date()->notNull(),
            'telegram_chat_id' => $this->string(), // Assuming Telegram chat ID is a string
            'position_id' => $this->string(),
            'reminder' => $this->integer()
            // Add other fields as needed
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%position}}');
    }
}
