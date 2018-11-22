<?php

namespace ale10257\translate\models;

use yii\db\ActiveRecord;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class ModelTranslate
 * @package ale10257\translate\models
 * @property int $id
 */
class ModelTranslate extends ActiveRecord
{
    /** @var array */
    protected $languages;
    /** @var string */
    protected $cacheKey;
    /** @var array */
    protected $tService;
    /** @var string */
    protected $language;
    /** @var string */
    protected $sourceLanguage;

    /** @inheritdoc */
    public static function tableName()
    {
        return 'ale10257_translate';
    }

    public function init()
    {
        $config = Yii::$app->ale10257Translate;
        $this->languages = $config->languages;
        $this->cacheKey = $config->cacheKey;
        $this->tService = $config->tService;
        $this->language = str_replace('-', '', Yii::$app->language);
        $this->sourceLanguage = str_replace('-', '', Yii::$app->sourceLanguage);
    }

    /**  @inheritdoc */
    public function rules()
    {
        return [
            [$this->languages, 'string'],
        ];
    }

    public function checkMsg($message, $language)
    {
        if (!self::find()->where([$language => $message])->count()) {
            $this->$language = $message;
            $this->save();
        }
    }

    public function createCache()
    {
        $cacheKey = $this->cacheKey;
        $cache = Yii::$app->cache;
        $sourceLanguage = $this->sourceLanguage;
        $data = self::find()->orderBy([$sourceLanguage => SORT_ASC])->all();
        $arr = [];
        foreach ($data as $value) {
            foreach ($this->languages as $language) {
                $arr[$language][$value->$sourceLanguage] = $value->$language;
            }
        }
        $cache->set($cacheKey, $arr);
        $this->createTService();
        return $arr;
    }

    private function createTService()
    {
        if (!$tService = $this->tService) {
            return;
        }
        $data = Yii::$app->cache->get($this->cacheKey);
        $sourceLanguage = $this->sourceLanguage;
        $data = $data[$sourceLanguage];
        $source = file_get_contents(__DIR__ . '/TService.php');
        $str = "\t" . 'public static $terms = [' . PHP_EOL;
        foreach ($data as $item) {
            $str .= "\t\t'$item'" . ' => ' . "'$item'," . PHP_EOL;
        }
        $str .= "\t" . '];';
        $replace = 'namespace ' . $tService['nameSpace'] . ';' . PHP_EOL . PHP_EOL . 'use ale10257\translate\models\ModelTranslate;';
        $source = str_replace(['namespace ale10257\translate\models;', 'public static $terms = [];'], [$replace, $str], $source);
        FileHelper::createDirectory($tService['path']);
        $file = FileHelper::normalizePath($tService['path'] . '/TService.php');
        file_put_contents($file, $source);
    }
}