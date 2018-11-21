<?php
namespace ale10257\translate;

use yii\base\Component;

class ConfigTranslate extends Component
{
    /** @var array  */
    public $languages = [];
    /** @var string  */
    public $cacheKey = 'translate';
    /** @var array  */
    public $tService = [];
}