<?php

namespace frontend\controllers;

use common\models\Employees;
use common\models\EmployeesSearch;
use CURLFile;
use Mosquitto\Client;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * EmployeesController implements the CRUD actions for Employees model.
 */
class EmployeesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Employees models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new EmployeesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employees model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Employees model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Employees();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->photo = UploadedFile::getInstance($model, 'photo');

            $employee = new Employees();
            $employee->name = $model->name;
            $employee->surname = $model->surname;
            $employee->date_of_birth = $model->date_of_birth;
            $employee->telegram_chat_id = $model->telegram_chat_id;

            if ($model->photo) {
                $res = $model->upload();
                if ($res) {
                    $employee->photo = $res;
                } else {
                    Yii::$app->session->setFlash('error', 'Error');
                }
            }
            Yii::$app->session->setFlash('success', 'Employee details saved successfully.');

            if (!$employee->save()) {
                Yii::$app->session->setFlash('error', $employee->getErrors());
            }

            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Employees model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldPhotoUrl = $model->photo;
        if ($this->request->isPost && $model->load($this->request->post())) {

            $model->photo = UploadedFile::getInstance($model, 'photo');

            if ($model->photo) {
                $res = $model->upload();

                if ($res) {
                    $model->photo = $res;
                } else {
                    Yii::$app->session->addFlash('error', 'Error');
                }
            } else {
                $model->photo = $oldPhotoUrl;
            }
            // Save employee data
            if ($model->save()) {
                // Redirect or do something else on successful save
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Employees model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Employees model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Employees the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employees::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionToggle($id)
    {
        $employee = Employees::findOne($id);

        if ($employee !== null) {
            $employee->reminder = $employee->reminder ? null : 1;

            if (!$employee->save()) {
                return $employee->getErrors();
            }

            return $this->redirect(Yii::$app->request->referrer);

        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionSend()
    {
        $today = date('m-d');
        $employees = Employees::find()->where("DATE_FORMAT(date_of_birth, '%m-%d') = '$today'")->all();

        foreach ($employees as $employee) {
            // Send Telegram message
            $employee->actionSendReminders();
        }
        Yii::$app->session->setFlash('success', 'Jonatildi');

        return $this->redirect(Yii::$app->request->referrer);

    }



}
