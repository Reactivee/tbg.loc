<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "employees".
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string|null $photo
 * @property string $date_of_birth
 * @property string|null $telegram_chat_id
 * @property string|null $position_id
 * @property int|null $reminder
 */
class Employees extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'date_of_birth'], 'required'],
            [['date_of_birth'], 'safe'],
            [['reminder'], 'integer'],
            [['photo'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
            [['name', 'surname', 'telegram_chat_id', 'position_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'surname' => 'Surname',
            'photo' => 'Photo',
            'date_of_birth' => 'Date Of Birth',
            'telegram_chat_id' => 'Telegram Chat ID',
            'position_id' => 'Position ID',
            'reminder' => 'Reminder',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            try {
                $directory = "uploads/";
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                    $handle = opendir($directory);
                }
                $generateName = uniqid();
                $fileName = $directory . $generateName . '.' . $this->photo->extension;

                $this->photo->saveAs($fileName);
                return '/' . $fileName;
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return false;
            }

        } else {
            return false;
        }
    }

    public function getPositionList()
    {
        $position = Position::find()->all();
        return ArrayHelper::map($position, 'id', 'name');
    }

    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
    }

    public function actionSendReminders()
    {

        $token = '6934084546:AAHOUC_rmiJB6krzr9jetTc2j7jWR96fcwA';
        $exist = false;

        $director = Employees::find()
            ->where(['reminder' => 1])
            ->andWhere(['not', ['id' => $this->id]])
            ->all();

        try {
            foreach ($director as $employ) {

                $tgRes = Yii::$app->telegram->getUpdates();

                foreach ($tgRes['result'] as $item) {
                    if ($employ->telegram_chat_id == $item['message']['chat']['id']) {
                        $exist = true;
                    }
                }

                $message = "Happy birthday ! 🎉 \nIsmi: {$this->name} \nFamiliyasi: {$this->surname} \nTug'ilgan yili 📆: {$this->date_of_birth} ";

                $baseUrl = Url::base(true);

                if ($exist) {
                    Yii::$app->telegram->sendPhoto([
                        'chat_id' => $employ->telegram_chat_id,
                        'photo' => $this->photo,
                        'caption' => $message
                    ]);
                }
            }
        } catch (\Exception $e) {
//            dd($e);
//            Yii::$app->session->setFlash('error', $e->getMessage());
            return false;

        }
        return true;

    }

}
