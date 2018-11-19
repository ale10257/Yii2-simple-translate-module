<?php
namespace ale10257\translate\widget;

use ale10257\translate\Translate;
use yii\base\Widget;

class TranslateWidget extends Widget
{
    public function run()
    {
        /** @var Translate $moduleTranslate */
        $moduleTranslate = \Yii::$app->getModule(TRANSLATE_MODULE);
        return $this->render('index', [
            'webPath' => $moduleTranslate->webIconsPath,
            'iconExt' => $moduleTranslate->iconExt,
            'language' => \Yii::$app->language,
        ]);
    }
}