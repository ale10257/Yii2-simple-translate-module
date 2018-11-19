<?php
namespace ale10257\translate;

use yii\base\BootstrapInterface;

class ConfigTranslate implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param \yii\base\Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if (isset($_COOKIE['language'])) {
            if (in_array($_COOKIE['language'], LANGUAGES)) {
                $app->language = $_COOKIE['language'];
            }
        }
    }
}