<?php

/** @var yii\web\View $this */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Theme';

?>


<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'name' => 'send-form']]); ?>

<div class="employees-form">

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'surname')->textInput() ?>

    <?= $form->field($model, 'photo')->fileInput() ?>

    <?= $form->field($model, 'date_of_birth')->textInput(['type' => 'date']) ?>

    <?= $form->field($model, 'telegram_chat_id')->textInput(['type'=>'number']) ?>
    <?= $form->field($model, 'position_id')->dropDownList($model->getPositionList()) ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
    </div>

</div>

<?php ActiveForm::end(); ?>

