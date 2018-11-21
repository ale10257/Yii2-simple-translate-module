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

```
composer require ale10257/yii2-simple-translate-module "@dev"
```

Соглашение об именовании полей таблицы для хранения терминов
--------------------------

Общепринятый формат для установки языка/локали: ll-CC, где ll — это двух или трёхбуквенный код языка в нижнем регистре в соответствии со стандартом ISO-639, а CC — это код страны в соответствии со стандартом ISO-3166.

Например, en-US, ru-Ru ...

Также в Yii2 допускается объявление локалей вида ru, de, en ...

<b>Важно:</b> поля таблицы для хранения терминов должны быть должны соответствовать правилам объявления переменных в языке php и правилам объявления локали (без дефиса).

Т.е., если в в конфигурационном файле приложения вы объявили локаль en-US, то поле должно именоваться enUS, если ru, то ru. C регистром ошибаться нельзя.  

----------------------------

<h4>Настраиваем файл конфигурации</h4>

```
<?php
...
$languages = [
    'Русский' => 'ru',
    'Deutsch' => 'de',
];
$sourceLanguage = $languages['Русский'];
$basePath = dirname(__DIR__);
$config = [
...
    'basePath' => $basePath,
    'language' => $sourceLanguage,
    'sourceLanguage' => $sourceLanguage,
...
    'components' => [
...
        'ale10257Translate' => [
            'class' => 'ale10257\translate\ConfigTranslate',
            'languages' => $languages,
            // ключ кеша может быть любым
            'cacheKey' => 'ale10257_translate', 
            // необязательная настройка tService, можно не передавать
            'tService' => [
                'nameSpace' => 'app\components\translate',
                'path' => $basePath . '/components/translate',
            ]
        ],
...
    ],
    'modules' => [
...        
        // название модуля может быть любым в рамках соглашений Yii2 о наименовании модулей
        'translate' => [
            'class' => 'ale10257\translate\Translate',
        ],
...        
    ], 
];
```

Создайте миграцию на основании локалей объявленных в `$languages` и вышеизложенного соглашения.

``
php yii migrate/create create_ale10257_translate_table --fields=ru:text,de:text
``

Применяем миграцию:

```
php yii migrate
```

<b>Важно:</b> при объявлении модуля `ale10257\translate\Translate` можно передать ряд настроек.

```
    ...
    'modules' => [
        ...
        'translate' => [
            'class' => 'ale10257\translate\Translate',
            'accessRules' => ['your-settings']
        ],
        ...
    ],
    ...
```

 Исходный код модуля `ale10257\translate\Translate`:
 
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

Т.е. можно изменить права доступа, переопределив `$accessRules` (по умолчанию модуль доступен только для зарегистрированных пользователей).

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

Как и в стандартной локализации Yii2, можно передавать параметры  в метод `t()`, например:

```
<?php
/**
 * @var $this \yii\web\View
 */
use ale10257\translate\models\TService;
echo TService::t( 'Крокодил летит на {path}', ['path' => 'хутор']);
```

<h5>Если вам лень печатать каждый раз длинные термины, и для предотвращения ошибок при наборе длинных терминов, можно организовать автодополнение для зарегистрированных терминов (проверено в IDE PhpStorm)</h5>

Для этого необходимо корректно настроить поле `tService` при объявлении компонента `ale10257Translate` в конфигурационном файле приложения, например:

```
$basePath = dirname(__DIR__);

...
    'components' => [
        'ale10257Translate' => [
            'class' => 'ale10257\translate\ConfigTranslate',
            'languages' => $languages,
            'cacheKey' => 'ale10257_translate',
            'tService' => [
                'nameSpace' => 'app\components\translate',
                'path' => $basePath . '/components/translate',
            ]
        ],
    ],
...

```

Смысл в том, что при каждом обновлении кеша для терминов, модуль `ale10257\translate\Translate` генерирует рабочий класс `TService` в указанной в настройках папке (`path`) с указанным пространством имен (`'nameSpace' => 'app\components\translate'`), в котором прописаны данные для автодополнения (в статическом поле `$term`).

Путь к директории и namespace должны соответствовать стандарту PSR-4. В приведенном примере новый класс `TService` будет располагаться по пути 

```
path-your-application/components/translate
```

С зарегистрированным пространством имен

```
app\components\translate
```

После этих настроек <b>нежелательно</b> объявлять термины "на лету"!

Заходим в браузере в модуль ale10257\translate\Translate

```
Url::to('/' . your-name-translate-module);
```

- Явно создаем термин, например 'Жирафы летят на юг'
- В нужном месте подключаем новый класс `TService` и обращаемся к его статистическому методу `t()`, используя статическое поле `$term`

Например:

```
<?php
use app\components\translate\TService;
echo TService::t(TService::$terms['Жирафы летят на юг']);
```

При наборе `TService::t(TService::$terms['Ж'])` выскочит автодополнение Жирафы летят на юг (при условии корректных настроек модуля).

<h4>Как добавить локаль</h4>

В `$languages` конфигурационного файла добавляем нужную локаль, например:

```
$languages = [
    'Русский' => 'ru',
    'Deutsch' => 'de',
    'English' => 'en-Us',
];
```

И, тем или иным способом добавляем в таблицу `ale10257_translate` текстовое поле, в данном случае enUS, например:

```
ALTER TABLE `ale10257_translate`
ADD `enUS` text NULL;
```

Обязательно сбрасываем кеш приложения, например:

```
php yii cache/flush-all
```














