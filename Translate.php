<?php

namespace ale10257\translate;

use yii\filters\AccessControl;

/**
 * Translate module definition class
 */
class Translate extends \yii\base\Module
{
    /** @var string */
    public $webIconsPath = 'icons';
    /** @var string */
    public $iconExt = 'png';
    /** @var array  */
    public $accessRules = [
        'allow' => true,
        'roles' => ['@'],
    ];
    /** @var string  */
    public $table = 'translate';
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
