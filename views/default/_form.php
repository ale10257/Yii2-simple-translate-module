<?php
/* @var $this yii\web\View */
/* @var $model ale10257\translate\models\ModelTranslate */
/* @var $form yii\widgets\ActiveForm */
/* @var string $action */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="model-translate-form">
    <?php $form = ActiveForm::begin([
        'action' => $action,
        'options' => [
            'id' => 'form-translate'
        ]
    ]); ?>
    <? foreach (LANGUAGES as $language) : ?>
        <?= $form->field($model, $language)->textarea(['rows' => 3])->label(strtoupper($language)) ?>
    <? endforeach ?>
    <div class="form-group text-right">
        <?= Html::submitButton('Ok', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
