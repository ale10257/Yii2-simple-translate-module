<?php
namespace ale10257\translate\models;

use yii\base\Model;

class ExcelForm extends Model
{
    /** @var string */
    public $file_xlsx;

    public function rules()
    {
        return [
            [['file_xlsx'], 'file', 'extensions' => 'xlsx'],
        ];
    }
}