<?php

use common\models\Employees;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var common\models\EmployeesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Employees';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employees-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Employees', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Test send message via telegram bot', ['send'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'surname',
            [
                'attribute' => 'photo',
                'format' => 'html',
                'value' => function ($model) {
//                            dd($model->photo);
                    return Html::img($model->photo, ['width' => '200', 'alt' => 'asd']);
                }
            ],
            'date_of_birth',
            //'telegram_chat_id',

            [
                'attribute' => 'position_id',
                'format' => 'html',
                'value' => function ($model) {

                    return $model->position->name;
                }
            ],
            [
                'attribute' => 'reminder',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->reminder) {
                        return '<span class="text-success">Yoniq</span>';
                    }
                    return "<span class='text-danger'>O'chiq</span>";


                }
            ],
            [
                'attribute' => 'reminder',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->reminder) {
                        return Html::a("Xabarnoma o'chirish", ['toggle', 'id' => $model->id, 'class' => '']);
                    }

                    return Html::a('Xabarnoma yoqish', ['toggle', 'id' => $model->id]);
                }
            ],


            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Employees $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
