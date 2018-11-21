<?php
namespace ale10257\translate;

use yii\base\BootstrapInterface;
use ale10257\translate\helpers\CheckLanguage;

class SetUpLang implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if (isset($_COOKIE['language'])) {
            $languages = CheckLanguage::check();
            if (in_array($_COOKIE['language'], $languages)) {
                $app->language = $_COOKIE['language'];
            }
        }
    }
}