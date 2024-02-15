<?php

namespace frontend\controllers;

use frontend\models\Receivers;
use frontend\models\UploadForm;
use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {

        $form = new UploadForm();


        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post())) {
            $session = new Session();
            $session->open();

            $form->file = UploadedFile::getInstance($form, 'file');
            $res = $form->upload();
            if ($res) {
                // file is uploaded successfully
                // Set the session variable with a link name
                $session['my_link_name'] = $res;
                // Redirect the user to a  page
                Yii::$app->session->setFlash('success', 'file is uploaded successfully');
                return $this->redirect(Yii::$app->request->referrer);
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }

        }

        return $this->render('index', [
            'model' => $form
        ]);
    }


    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionSend()
    {
        $model = new Receivers();

        //handle post request
        if (Yii::$app->request->post() && $model->load(Yii::$app->request->post())) {

            $session = new Session();
            $session->open();
            $linkName = $session->get('my_link_name');
            try {
                if ($linkName && file_exists($linkName)) {
                    $model->file = $linkName;

                    $response = false;
                    if (filter_var($model->email_tg, FILTER_VALIDATE_EMAIL)) {
                        //send via email
                        $response = UploadForm::sendEmailWithAttachment($model);
                    }
                    if (filter_var($model->email_tg, FILTER_VALIDATE_INT)) {
                        //send via telegram
                        $response = UploadForm::sendDocTg($model);
                    }

                    if (!$response) {
                        Yii::$app->session->addFlash('error', 'не найдено tg/email');
                        return $this->redirect(Yii::$app->request->referrer);
                    }

                    if (!$model->save()) {
                        $errors = $model->getErrors();
                        $errorMessages = array_shift($errors);
                        Yii::$app->session->setFlash('error', implode('<br>', $errorMessages));
                        return $this->redirect(Yii::$app->request->referrer);
                    } else {
                        unlink($linkName);
                        $session->remove('my_link_name');
                        Yii::$app->session->addFlash('success', 'Отправлено');
                    }

                } else {
                    Yii::$app->session->addFlash('error', 'Файл не найдено');
                }
            } catch (\Exception $e) {
                Yii::$app->session->addFlash('error', $e->getMessage());
                return $this->redirect(Yii::$app->request->referrer);

            }
            return $this->redirect('/');
        }

        return $this->render('about', [
            'model' => $model,
        ]);
    }


}
