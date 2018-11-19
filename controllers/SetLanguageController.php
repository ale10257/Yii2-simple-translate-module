<?php
namespace ale10257\translate\controllers;

use app\controllers\BaseController;

class SetLanguageController extends BaseController
{
    public function actionIndex($language)
    {
        if (in_array($language, LANGUAGES)) {
            setcookie('language', $language, time() + (3600 * 24 * 360), '/');
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}