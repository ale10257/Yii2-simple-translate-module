<?php
namespace ale10257\translate\assets;

use yii\web\AssetBundle;

class TranslateAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/';

    public $js = [
        'translate.js'
    ];

    public $css = [
        'translate.css'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}