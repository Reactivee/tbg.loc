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


}