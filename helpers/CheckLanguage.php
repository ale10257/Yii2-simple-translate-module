<?php
namespace ale10257\translate\helpers;

class CheckLanguage
{
    public static function check()
    {
        $languages = \Yii::$app->ale10257Translate->languages;
        foreach ($languages as $key => $language) {
            $check = preg_split('/(?<=[a-z])(?=[A-Z])/x', $language);
            if (count($check) > 1) {
                $languages[$key] = implode('-', $check);
            }
        }
        return $languages;
    }
}