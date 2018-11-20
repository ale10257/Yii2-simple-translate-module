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

<b>Важно:</b> названия полей ru, de, en, es ... в таблице ale10257_translate должны соответствовать правилам объявления переменных в языке php. 

<b>Неверно (ru-RU, de-DE)</b>:

``
yii migrate/create create_ale10257_translate_table --fields=ru-RU:text,de-DE:text
``

<b>Верно (ruRU, deDE)</b>:

``
yii migrate/create create_ale10257_translate_table --fields=ruRU:text,deDE:text
``

Применяем миграцию:

``
php yii migrate
``

В конфигурационном файле приложения объявляем константы LANGUAGES и TRANSLATE_MODULE

```
<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
defined('LANGUAGES') or define('LANGUAGES', [
    'Русский' => 'ru',
    'Deutsch' => 'de',
]);
defined('TRANSLATE_MODULE') or define('TRANSLATE_MODULE', 'translate');
...
```
<b>Важно:</b> в константе LANGUAGES значения массива должны соответствовать полям таблицы ale10257_translate (ru, de ...)

<b>Важно:</b> Значение константы TRANSLATE_MODULE должно соответствовать правилам наименования модулей в Yii2. Например: 'translate', 'my-translate', 'myTranslate'

<b>Важно:</b> обязательно объявляем в конфигурационном файле приложения $language и $sourceLanguage, например:
```
$config = [
    ...
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    ...
];
```

Также в конфигурационном файле приложения в секции modules объявляем модуль 'ale10257\translate\Translate':
```
    ...
    'modules' => [
        ...
        TRANSLATE_MODULE => [
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
    /** @var array  */
    public $tService = [];

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

Т.е. можно изменить права доступа, переопределив $accessRules (по умолчанию модуль доступен только для зарегистрированных пользователей), и настроить $tService.

<h4>После выполнения всех настроек модуль доступен по адресу</h4>

```
Url::to('/' . TRANSLATE_MODULE);
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

Для этого необходимо корректно настроить поле tService при объявлении модуля TRANSLATE_MODULE в конфигурационном файле приложения, например:

```
    ...
   $basePath = dirname(__DIR__);
   $tServiceDir = $basePath . '/components/translate';
   defined('LANGUAGES') or define('LANGUAGES', [
       'Русский' => 'ru',
       'Deutsch' => 'de',
   ]);
   defined('TRANSLATE_MODULE') or define('TRANSLATE_MODULE', 'translate');
   
   $config = [
       'basePath' => $basePath,
       'language' => 'ru',
       'sourceLanguage' => 'ru',
       'components' => [
            ...
       ],
       'modules' => [
       ...
           TRANSLATE_MODULE => [
               'class' => 'ale10257\translate\Translate',
               'tService' => [
                   'nameSpace' => 'app\components\translate',
                   'path' => $tServiceDir,
               ]
           ],
       ...
       ],
   ];
```

Смысл в том, что при каждом обновлении кеша для терминов, модуль 'ale10257\translate\Translate' генерирует рабочий класс TService в указанной в настройках папке ('path' => $tServiceDir,) с указанным пространством имен ('nameSpace' => 'app\components\translate'), в котором прописаны данные для автодополнения (в статическом поле $term).

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

<h3>Известные проблемы</h3>
В связи с тем, что работа модуля жестко завязана на кеш приложения, при возникновении проблем очищаем кеш тем, или иным способом.












