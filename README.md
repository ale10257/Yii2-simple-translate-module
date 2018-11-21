Простой модуль для организации системы переводов на сайте для Yii2
=======================
Цель создания модуля: научиться сохранять на "лету" необходимые термины. 

Например, на сайте должно быть два языка - ru и de. 
В нужном месте мы пишем нужный термин, например:
```
TService::t('Крокодилы летят на север')
```
Необходимо, чтобы при первом вызове данного метода, термин 'Крокодилы летят на север' <b>автоматически</b> записался в БД и в кеш.

При создании, удалении, обновлении терминов кеш должен перезаписываться. Все термины должны браться из кеша приложения.

Также необходимо сформировать файл excel c терминами для переводчика(ов), и загрузить его обратно на сервер

Установка
------------------------
`
composer require ale10257/yii2-simple-translate-module "*"
`

В директории с проектом Yii2 запустите команду для создания миграции с нужными полями (локалями ru, de ...)

``
yii migrate/create create_ale10257_translate_table --fields=ru:text,de:text
``

<b>Важно:</b> названия полей ru, de, en, es ... в таблице ale10257_translate должны соответствовать правилам объявления переменных в языке php и правилам объявления локали в Yii2 (без дефиса)! 

<b>Неверно (ru-RU, de-DE)</b>:

``
yii migrate/create create_ale10257_translate_table --fields=ru-RU:text,de-DE:text
``

<b>Неверно (ruRu, deDe)</b>:

``
yii migrate/create create_ale10257_translate_table --fields=ruRu:text,deDe:text
``

<b>Верно (ruRU, deDE)</b>:

``
yii migrate/create create_ale10257_translate_table --fields=ruRU:text,deDE:text
``

Применяем миграцию:

``
php yii migrate
``

В конфигурационном файле приложения в секции components объявляем и настраиваем компонент ale10257Translate

```
    'components' => [
        'ale10257Translate' => [
            'class' => 'ale10257\translate\ConfigTranslate',
            'languages' => [
                'Русский' => 'ru',
                'Deutsch' => 'de',
            ],
            'cacheKey' => 'ale10257_translate',
            'tService' => []
        ],
    ],
...

```

По настройкам компонента ale10257Translate:
- значения массива ['Русский' => 'ru', 'Deutsch' => 'de',], должны соответствовать полям таблицы ale10257_translate (ru, ruRU, enUS ...), ключи могут быть произвольными.
- поле cacheKey - произвольное.
- про настройку tService - ниже (необязательное поле)

<b>Не забываем объявить</b> в конфигурационном файле приложения $language и $sourceLanguage, например:
```
$config = [
    ...
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    ...
];
```

Также в конфигурационном файле приложения в секции modules объявляем модуль 'ale10257\translate\Translate' (название модуля может быть любым и должно соответствовать принятым в Yii2 правилам):
```
    ...
    'modules' => [
        ...
        'translate' => [
            'class' => 'ale10257\translate\Translate',
        ],
        ...
    ],
    ...
```
<b>Важно:</b> при объявлении модуля 'ale10257\translate\Translate' можно передать ряд настроек. Исходный код модуля 'ale10257\translate\Translate':
```
/**
 * Translate module definition class
 */
class Translate extends \yii\base\Module
{
    /** @var array  */
    public $accessRules = [
        'allow' => true,
        'roles' => ['@'],
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    $this->accessRules,
                ],
            ],
        ];
    }
}
```

Т.е. можно изменить права доступа, переопределив $accessRules (по умолчанию модуль доступен только для зарегистрированных пользователей).

<h4>После выполнения всех настроек модуль доступен по адресу</h4>

```
Url::to('/' . your-name-translate-module);
```

Можно генерировать термины "на лету", работать с файлами excel. Для генерации терминов в нужном файле подключаем класс ale10257\translate\models\TService, и вызываем его статический метод t:

```
<?php
/**
 * @var $this \yii\web\View
 */
use ale10257\translate\models\TService;
echo TService::t( 'Крокодилы летят на север');
```

Как и в стандартной локализации Yii2, можно передавать параметры  в метод t, например:

```
<?php
/**
 * @var $this \yii\web\View
 */
use ale10257\translate\models\TService;
echo TService::t( 'Крокодил летит на {path}', ['path' => 'хутор']);
```

<h5>Если вам лень печатать каждый раз длинные термины, и для предотвращения ошибок при наборе длинных терминов, можно организовать автодополнение для зарегистрированных терминов (проверено в IDE PhpStorm)</h5>

Для этого необходимо корректно настроить поле tService при объявлении компонента ale10257Translate в конфигурационном файле приложения, например:

```
$basePath = dirname(__DIR__);

...
    'components' => [
        'ale10257Translate' => [
            'class' => 'ale10257\translate\ConfigTranslate',
            'languages' => [
                'Русский' => 'ru',
                'Deutsch' => 'de',
            ],
            'cacheKey' => 'ale10257_translate',
            'tService' => [
                'nameSpace' => 'app\components\translate',
                'path' => $basePath . '/components/translate',
            ]
        ],
    ],
...

```

Смысл в том, что при каждом обновлении кеша для терминов, модуль 'ale10257\translate\Translate' генерирует рабочий класс TService в указанной в настройках папке ('path') с указанным пространством имен ('nameSpace' => 'app\components\translate'), в котором прописаны данные для автодополнения (в статическом поле $term).

Путь к директории и namespace должны соответствовать стандарту PSR-4. В приведенном примере новый класс TService будет располагаться по пути 

```
path-your-application/components/translate
```

С зарегистрированным пространством имен

```
app\components\translate
```

После этих настроек <b>очень нежелательно</b> объявлять термины "на лету"!

Заходим в браузере в модуль ale10257\translate\Translate

```
Url::to('/' . TRANSLATE_MODULE);
```

- Явно создаем термин, например 'Жирафы летят на юг'
- В нужном месте подключаем новый класс TService и обращаемся к его статистическому методу t(), используя статическое поле $term

Например:

```
<?php
use app\components\translate\TService;
echo TService::t(TService::$terms['Жирафы летят на юг']);
```

При наборе TService::t(TService::$terms['Ж']) выскочит автодополнение Жирафы летят на юг (при условии корректных настроек модуля).

<h4>Как добавить локаль</h4>

Тем или иным способом добавляем в таблицу ale10257_translate текстовое поле, например enUS, по умолчанию NULL, например:

```
ALTER TABLE `ale10257_translate`
ADD `enUS` text NULL;
```

И точно такое же значение добавляем в поле languages компонента ale10257Translate в конфигурационном файле приложения

```
$basePath = dirname(__DIR__);
...
    'components' => [
        'ale10257Translate' => [
            'class' => 'ale10257\translate\ConfigTranslate',
            'languages' => [
                'Русский' => 'ru',
                'Deutsch' => 'de',
                'English' => 'enUS'
            ],
            'cacheKey' => 'ale10257_translate',
            'tService' => [
                'nameSpace' => 'app\components\translate',
                'path' => $basePath . '/components/translate',
            ]
        ],
    ],
...

```

Обязательно сбрасываем кеш приложения, например:

```
php yii cache/flush-all
```














