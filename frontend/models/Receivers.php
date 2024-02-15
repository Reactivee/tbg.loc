<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "receivers".
 *
 * @property int $id
 * @property string $username
 * @property string $email_tg
 * @property int|null $created_at
 */
class Receivers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'receivers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email_tg', 'file'], 'required'],
            [['created_at'], 'integer'],
            [['username', 'email_tg', 'file'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email_tg' => 'Email Tg',
            'created_at' => 'Created At',
        ];
    }
}
