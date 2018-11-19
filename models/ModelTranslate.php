<?php
namespace ale10257\translate\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Class ModelTranslate
 * @package ale10257\translate\models
 * @property int $id
 */
class ModelTranslate extends ActiveRecord
{
    /** @inheritdoc */
    public static function tableName()
    {
        /** @var \ale10257\translate\Translate $module */
        $module = \Yii::$app->getModule(TRANSLATE_MODULE);
        return $module->table;
    }

    /**  @inheritdoc */
    public function rules()
    {
        return [
            [LANGUAGES, 'string'],
        ];
    }

    public static function getMsg($message)
    {
        $language = Yii::$app->language;
        if (!in_array($language, LANGUAGES)) {
            throw new \DomainException('Language ' . $language . ' not found in ' . __METHOD__);
        }
        $sourceLanguage = Yii::$app->sourceLanguage;
        $cache = Yii::$app->cache;
        $key = TRANSLATE_MODULE;
        if (!$translate = $cache->get($key)) {
            if ($data = self::find()->orderBy([$sourceLanguage => SORT_ASC])->all()) {
                foreach ($data as $item) {
                    foreach (LANGUAGES as $language) {
                        $translate[$language][$item->$sourceLanguage] = $item->$language;
                    }
                }
                $cache->set($key, $translate);
                //self::createTService();
            }
        }
        if (!isset($translate[$sourceLanguage][$message])) {
            if (!self::find()->where([$sourceLanguage => $message])->count()) {
                $model = new self;
                $model->$sourceLanguage = $message;
                if (!$model->save()) {
                    throw new \DomainException('Language ' . $message . ' save error!');
                }
            }
            $translate[$sourceLanguage][$message] = $message;
            $cache->set($key, $translate);
            //self::createTService();
        }
        if (empty($translate[$language][$message])) {
            return $translate[$sourceLanguage][$message];
        }
        return $translate[$language][$message];
    }

    private static function createTService()
    {
        $data = Yii::$app->cache->get(TRANSLATE_MODULE);
        $data = $data[Yii::$app->sourceLanguage];
        $str = '<?php' . PHP_EOL . 'namespace ' . __NAMESPACE__ . ';' . PHP_EOL . PHP_EOL;
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
        $tService = __DIR__ . '/TService.php';
        file_put_contents($tService, $str);
    }
}