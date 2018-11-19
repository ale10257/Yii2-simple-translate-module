<?php
/**
 * @var \yii\web\View $this
 * @var \ale10257\translate\models\ExcelForm $excelModel
 */

use yii\widgets\ActiveForm;
use ale10257\translate\models\TService;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php $form = ActiveForm::begin([
    'action' => Url::to(['update-from-excel']),
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'form-inline',],

]);
?>
<?= $form->field($excelModel, 'file_xlsx')->fileInput()->label(TService::t('Загрузить файл excel с переводами')); ?>
<?= Html::submitButton(TService::t('Загрузить файл'), ['class' => 'btn btn-primary']) ?>


<?php ActiveForm::end(); ?>
