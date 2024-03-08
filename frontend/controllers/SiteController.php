<?php

namespace frontend\controllers;
;

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
        return $this->redirect('/employees/index');

    }


}
