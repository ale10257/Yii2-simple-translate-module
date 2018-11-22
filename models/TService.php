<?php

namespace ale10257\translate\models;
use Yii;

class TService
{
    public static $terms = [];

    public static function t($message, $params = [])
    {
        $language = str_replace('-', '', Yii::$app->language);
        $sourceLanguage = str_replace('-', '', Yii::$app->sourceLanguage);
        $cacheKey = Yii::$app->ale10257Translate->cacheKey;
        if (!$cache = Yii::$app->cache->get($cacheKey)) {
            $model = new ModelTranslate();
            $cache = $model->createCache();
        }
        if (!isset($cache[$sourceLanguage][$message])) {
            $model = new ModelTranslate();
            $model->checkMsg($message, $sourceLanguage);
            $cache = $model->createCache();
        }
        $msg = $cache[$language][$message] ? : $cache[$sourceLanguage][$message];
        $placeholders = [];
        if ($params) {
            foreach ($params as $name => $value) {
                $placeholders['{' . $name . '}'] = $value;
            }
        }
        return !$placeholders ? $msg : strtr($msg, $placeholders);
    }
}