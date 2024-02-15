<?php


namespace frontend\models;


use CURLFile;
use Yii;
use yii\base\Model;

class UploadForm extends Model
{
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf',],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            try {
                $directory = "uploads";
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                    $handle = opendir($directory);
                }
                $generateName = uniqid();
                $fileName = 'uploads/' . $generateName . '.' . $this->file->extension;
                $this->file->saveAs($fileName);
                return $fileName;
            } catch (\Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return false;
            }

        } else {
            return false;
        }
    }

    public static function sendDocTg($model)
    {

        $token = '6934084546:AAHOUC_rmiJB6krzr9jetTc2j7jWR96fcwA';
        $exist = false;


        try {
            $res = Yii::$app->telegram->getUpdates();
            foreach ($res['result'] as $item) {
                if ($model->email_tg == $item['message']['chat']['id']) {
                    $exist = true;
                }
            }
            if ($exist) {

                // Initialize cURL
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $token . '/sendDocument');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'chat_id' => $model->email_tg,
                    'document' => new CURLFile($model->file)
                ]);

                // Execute cURL request
                $result = curl_exec($ch);

                // Check for errors
                if (curl_errno($ch)) {
                    return 'Error:' . curl_error($ch);
                }
                // Close cURL resource
                curl_close($ch);
                return json_encode($result);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return false;

        }
        return false;

    }

    // Assuming you have a controller action where you want to send the email
    public static function sendEmailWithAttachment($model)
    {

        try {
            // Path to the file you want to attach
            $filePath = $model->file;

            // Email details
            $toEmail = $model->email_tg;
            $subject = 'Email with Attachment';
            $body = 'Please find the attached file.';

            // Create a new Yii mail message
            $message = Yii::$app->mailer->compose();

            // Set email parameters
            $message->setTo($toEmail);
            $message->setSubject($subject);
            $message->setTextBody($body);

            // Attach the file
            $message->attach($filePath);
            // Send the email
            if ($message->send()) {
                return true;
            }
        } catch
        (\Exception $e) {
            return $e->getMessage();
        }
        return false;

    }

}