<?php

namespace ale10257\translate\models;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use yii\web\UploadedFile;
use Yii;

class Excel
{
    /** @var string */
    private $startAlphabet = 'A';

    public function create()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $col = $this->startAlphabet;
        foreach (Yii::$app->ale10257Translate->languages as $language) {
            $sheet->setCellValue($col . 1, $language);
            $col++;
        }
        $sourceLanguage = str_replace('-', '', \Yii::$app->sourceLanguage);
        foreach (ModelTranslate::find()->orderBy([$sourceLanguage => SORT_ASC])->all() as $key => $item) {
            $col = $this->startAlphabet;
            foreach (Yii::$app->ale10257Translate->languages as $language) {
                $sheet->setCellValue($col . ($key + 2), $item->$language);
                $col++;
            }
        }
        $writer = new Writer($spreadsheet);
        $fileName = 'translate-' . date('Y-m-d', time()) . '.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    public function updateData(UploadedFile $file)
    {
        $reader = new Reader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file->tempName);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumn++;
        $languages = [];
        $row = 1;
        $data = [];
        $i = 0;
        $col = $this->startAlphabet;
        foreach (Yii::$app->ale10257Translate->languages as $language) {
            $lang = $worksheet->getCell($col . $row)->getValue();
            if (in_array($lang, Yii::$app->ale10257Translate->languages)) {
                $languages[$i] = $language;
                $col++;
            }
            $i++;
        }
        if (!$languages) {
            throw new \DomainException('Languages not found! ' . __METHOD__);
        }
        for ($row = 2; $row <= $highestRow; ++$row) {
            $a = [];
            $i = 0;
            for ($col = $this->startAlphabet; $col != $highestColumn; $col++) {
                if (isset($languages[$i])) {
                    $a[$languages[$i]] = $worksheet->getCell($col . $row)->getValue();
                    $i++;
                }
            }
            if ($a) {
                $data[] = $a;
            }
        }
        if ($data) {
            ModelTranslate::deleteAll();
            \Yii::$app->db->createCommand()->batchInsert(ModelTranslate::tableName(), $languages, $data)->execute();
            \Yii::$app->cache->delete(Yii::$app->ale10257Translate->cacheKey);
        }
    }
}