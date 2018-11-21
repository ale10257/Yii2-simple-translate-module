<?php
namespace ale10257\translate\controllers;

use Yii;
use ale10257\translate\models\ModelTranslate;
use ale10257\translate\models\Search;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use ale10257\translate\models\Excel;
use ale10257\translate\models\ExcelForm;
use yii\web\UploadedFile;
use yii\helpers\Url;

class DefaultController extends Controller
{
    public function behaviors(){
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'excelModel' => new ExcelForm(),
        ]);
    }

    public function actionUpdateFromExcel()
    {
        $model = new ExcelForm();
        if ($model->load(\Yii::$app->request->post())) {
            if ($model->file_xlsx = UploadedFile::getInstance($model, 'file_xlsx')) {
                $excel = new Excel();
                $excel->updateData($model->file_xlsx);
            }
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'success' => [],
            'error' => false
        ];
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            foreach (\Yii::$app->ale10257Translate->languages as $language) {
                $result['success'][] = [
                    'id' => '#' . $language . '-' . $model->id,
                    'value' => $model->$language,
                ];
            }
            \Yii::$app->cache->delete(Yii::$app->ale10257Translate->cacheKey);
        } else {
            $result['error'] = implode(PHP_EOL, $model->firstErrors);
        }

        return $result;
    }

    public function actionInsert()
    {
        $model = new ModelTranslate();
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'reload' => false,
            'error' => false
        ];
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            $result['reload'] = true;
            \Yii::$app->cache->delete(Yii::$app->ale10257Translate->cacheKey);
        } else {
            $result['error'] = implode(PHP_EOL, $model->firstErrors);
        }

        return $result;
    }

    public function actionGetForm()
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $id = \Yii::$app->request->post('id');
            $action = \Yii::$app->request->post('action');
            $model = $id ? $this->findModel($id) : new ModelTranslate();
            if ($action == 'update') {
                $action = Url::to(['update', 'id' => $id]);
            }
            if ($action == 'insert') {
                $action = Url::to(['insert']);
            }
            $form = $this->renderAjax('_form', ['model' => $model, 'action' => $action]);
            return ['form' => $form];
        }
        return $this->redirect(\Yii::$app->request->referrer);
    }

    public function actionGenerateExcel()
    {
        $excel = new Excel();
        $excel->create();
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \Yii::$app->cache->delete(Yii::$app->ale10257Translate->cacheKey);
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ModelTranslate::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
