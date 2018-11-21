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

    public function __construct(array $config = [])
    {
        parent::__construct($config);
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

    public static function getMsg($message)
    {
        $self = new self;
        $language = $self->language;
        $sourceLanguage = $self->sourceLanguage;
        if (!in_array($language, $self->languages)) {
            throw new \DomainException('Language ' . $language . ' not found in ' . __METHOD__);
        }
        $cache = Yii::$app->cache;
        $key = $self->cacheKey;
        if (!$translate = $cache->get($key)) {
            if ($data = self::find()->orderBy([$sourceLanguage => SORT_ASC])->all()) {
                foreach ($data as $item) {
                    foreach ($self->languages as $language) {
                        $translate[$language][$item->$sourceLanguage] = $item->$language;
                    }
                }
                $cache->set($key, $translate);
                self::createTService();
            }
        }
        if (!isset($translate[$sourceLanguage][$message])) {
            if (!self::find()->where([$sourceLanguage => $message])->count()) {
                $self->$sourceLanguage = $message;
                if (!$self->save()) {
                    throw new \DomainException('Language ' . $message . ' save error!');
                }
            }
            $translate[$sourceLanguage][$message] = $message;
            $cache->set($key, $translate);
            self::createTService();
        }
        if (empty($translate[$language][$message])) {
            return $translate[$sourceLanguage][$message];
        }
        return $translate[$language][$message];
    }

    private static function createTService()
    {
        $self = new self;
        if (!$tService = $self->tService) {
            return;
        }
        $data = Yii::$app->cache->get($self->cacheKey);
        $sourceLanguage = $self->sourceLanguage;
        $data = $data[$sourceLanguage];
        $str = '<?php' . PHP_EOL . 'namespace ' . $tService['nameSpace'] . ';' . PHP_EOL;
        $str .= 'use ale10257\translate\models\ModelTranslate;' . PHP_EOL . PHP_EOL;
        $str .= 'class TService' . PHP_EOL . '{' . PHP_EOL . "\t" . 'public static $terms = [' . PHP_EOL;
        foreach ($data as $item) {
            $str .= "\t\t'$item'" . ' => ' . "'$item'," . PHP_EOL;
        }
        $str .= "\t" . '];' . PHP_EOL . PHP_EOL;
        $str .= "\t" . 'public static function t($message, $params = [])' . PHP_EOL . "\t" . '{' . PHP_EOL;
        $str .= "\t\t" . '$message = ModelTranslate::getMsg($message);' . PHP_EOL;
        $str .= "\t\t" . '$placeholders = [];' . PHP_EOL;
        $str .= "\t\t" . 'if ($params) {' . PHP_EOL;
        $str .= "\t\t\t" . 'foreach ($params as $name => $value) {' . PHP_EOL;
        $str .= "\t\t\t\t" . '$placeholders[\'{\' . $name . \'}\'] = $value;' . PHP_EOL;
        $str .= "\t\t\t" . '}' . PHP_EOL;
        $str .= "\t\t" . '}' . PHP_EOL;
        $str .= "\t\t" . 'return !$placeholders ? $message : strtr($message, $placeholders);' . PHP_EOL;
        $str .= "\t" . '}' . PHP_EOL;
        $str .= '}';
        FileHelper::createDirectory($tService['path']);
        $file = FileHelper::normalizePath($tService['path'] . '/TService.php');
        file_put_contents($file, $str);
    }
}