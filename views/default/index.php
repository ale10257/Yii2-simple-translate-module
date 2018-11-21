<?php
/**
 * @var $this yii\web\View
 * @var $searchModel ale10257\translate\models\Search
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var \ale10257\translate\models\ExcelForm $excelModel
 */

use yii\helpers\Html;
use yii\grid\GridView;
use ale10257\translate\models\ModelTranslate;
use yii\helpers\Url;
use ale10257\translate\assets\TranslateAsset;

$terms = [
    'translations' => [
        'ru' => 'Переводы',
        'en' => 'Translations',
    ],
    'edit_terms' => [
        'ru' => 'Для редактирования кликните по нужной ячейке таблицы',
        'en' => 'To edit, click on the desired cell in the table',
    ],
    'clear_result_search' => [
        'ru' => 'Сбросить результаты поиска',
        'en' => 'Reset search results',
    ],
    'generate_excel' => [
        'ru' => 'Сгенерировать файл excel',
        'en' => 'Generate excel file',
    ],
    'download_excel' => [
        'ru' => 'Загрузить файл excel с переводами',
        'en' => 'Download excel file with translations',
    ],
    'download' => [
        'ru' => 'Загрузить',
        'en' => 'Download',
    ],
    'add_term' => [
        'ru' => 'Добавить термин',
        'en' => 'Add term',
    ],
    'edit_term' => [
        'ru' => 'Редактировать перевод',
        'en' => 'Edit translation',
    ],
];
$source = (Yii::$app->sourceLanguage == 'ru' || Yii::$app->sourceLanguage == 'ru-RU') ? 'ru' : 'en';
TranslateAsset::register($this);
$this->title = $terms['translations'][$source];
$this->params['breadcrumbs'][] = $this->title;
$columns[] = ['class' => 'yii\grid\SerialColumn'];
foreach (Yii::$app->ale10257Translate->languages as $language) {
    $columns[] = [
        'label' => $language,
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
        <h4><?= $terms['edit_terms'][$source] ?></h4>
        <p>
            <?= Html::a($terms['clear_result_search'][$source], ['index'], ['class' => 'btn btn-danger d-inline']) ?>
            <?= Html::a($terms['generate_excel'][$source], ['generate-excel'],
                ['class' => 'btn btn-success d-inline']) ?>
            <?= Html::a($terms['add_term'][$source], ['get-form'],
                ['class' => 'btn btn-info d-inline', 'data-action' => 'insert', 'id' => 'insert-term']) ?>
        </p>
        <p><?= $this->render('_excel_form',
                ['excelModel' => $excelModel, 'terms' => $terms, 'source' => $source]) ?></p>
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
                <h4 class="modal-title"><?= $terms['edit_term'][$source] ?></h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>