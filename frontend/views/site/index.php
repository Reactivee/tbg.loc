<?php

/** @var yii\web\View $this */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'TBG';
/** @var \frontend\models\UploadForm $model */
/** @var \frontend\models\UploadForm $link */
$file_link = $_SESSION['my_link_name'];
?>
<h1>Прикрепить файл</h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'name' => 'send-form']]); ?>
<?= $form->field($model, 'file')->fileInput()->label(false) ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-success mt-2']) ?>
<?
if ($file_link) {
    echo Html::a('Отправить', '/site/send', ['class' => 'btn btn-warning mt-2']);
    echo Html::a('Посмотреть', $file_link, ['class' => 'btn btn-danger mt-2 mx-2','target'=>'_blank']);
}
?>

<?php ActiveForm::end(); ?>

