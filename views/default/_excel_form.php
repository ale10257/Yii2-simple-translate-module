<?php
/**
 * @var \yii\web\View $this
 * @var \ale10257\translate\models\ExcelForm $excelModel
 * @var array $terms
 * @var string $source
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $form = ActiveForm::begin([
    'action' => Url::to(['update-from-excel']),
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-inline',],

]);
?>
<?= $form->field($excelModel, 'file_xlsx')->fileInput()->label($terms['download_excel'][$source]); ?>
<?= Html::submitButton($terms['download'][$source], ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
