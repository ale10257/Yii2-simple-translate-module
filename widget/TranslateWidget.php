<?php

namespace ale10257\translate\widget;

use yii\base\Widget;
use yii\helpers\Url;
use ale10257\translate\helpers\CheckLanguage;

class TranslateWidget extends Widget
{
    /** @var string */
    public $webIconsPath = 'icons';
    /** @var string */
    public $iconExt = 'png';
    /** @var string */
    public $urlChangeLanguage;

    /** @var string */
    private $language;

    public function run()
    {
        $this->language = \Yii::$app->language;
        if (!$this->urlChangeLanguage) {
            $this->urlChangeLanguage = Url::to([
                '/translate/set-language',
                'language' => $this->language
            ]);
        }
        return $this->render('index', [
            'webPath' => $this->webIconsPath,
            'iconExt' => $this->iconExt,
            'language' => $this->language,
            'languages' => CheckLanguage::check(),
        ]);
    }
}