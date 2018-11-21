<?php
namespace ale10257\translate;

use yii\base\Component;

class ConfigTranslate extends Component
{
    /** @var array  */
    public $sourceLanguages = [];
    /** @var array  */
    public $languages = [];
    /** @var string  */
    public $cacheKey = 'translate';
    /** @var array  */
    public $tService = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->sourceLanguages = $this->languages;
        foreach ($this->languages as $key => $language) {
            $this->languages[$key] = str_replace('-', '', $language);
        }
    }
}