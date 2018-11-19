<?php
/* @var $this yii\web\View */
/* @var $searchModel ale10257\translate\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

/** @var \ale10257\translate\models\ExcelForm $excelModel */

use yii\helpers\Html;
use yii\grid\GridView;
use ale10257\translate\models\TService;
use ale10257\translate\models\ModelTranslate;
use yii\helpers\Url;
use ale10257\translate\assets\TranslateAsset;

TranslateAsset::register($this);
$this->title = TService::t('Переводы');
$this->params['breadcrumbs'][] = $this->title;
$columns[] = ['class' => 'yii\grid\SerialColumn'];
foreach (LANGUAGES as $language) {
    $columns[] = [
        'label' => strtoupper($language),
        'attribute' => $language,
        'contentOptions' => function ($model) use ($language) {
            /** @var ModelTranslate $model */
            return [
                'id' => $language . '-' . $model->id,
                'class' => 'translate-td',
                'data-action' => 'update',
                'data-key' => $model->id,
            ];
        },
        'value' => function ($model) use ($language) {
            return $model->$language;
        }
    ];
}
$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{delete}'
];
?>

<div class="row">
    <div class="col-md-12">
        <h2><?= $this->title ?></h2>
        <h4><?= TService::t('Для редактирования кликните по нужной ячейке таблицы') ?></h4>
        <p>
            <?= Html::a(TService::t('Сбросить результаты поиска'), ['index'], ['class' => 'btn btn-danger d-inline']) ?>
            <?= Html::a(TService::t('Сгенерировать файл excel'), ['generate-excel'],
                ['class' => 'btn btn-success d-inline']) ?>
            <?= Html::a(TService::t('Добавить термин'), ['get-form'],
                ['class' => 'btn btn-info d-inline', 'data-action' => 'insert', 'id' => 'insert-term']) ?>
        </p>
        <p><?= $this->render('_excel_form', ['excelModel' => $excelModel]) ?></p>
        <div class="box">
            <div class="box-body">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-striped table-bordered', 'id' => 'translate-table'],
                    'rowOptions' => ['class' => 'translate-tr'],
                    'columns' => $columns
                ]); ?>
            </div>
        </div>
    </div>
</div>

<div data-url="<?= Url::to(['get-form']) ?>" id="translate-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= TService::t('Редактировать перевод') ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>