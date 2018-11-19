<?php
namespace ale10257\translate\models;

use yii\data\ActiveDataProvider;

class Search extends ModelTranslate
{
    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = ModelTranslate::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [\Yii::$app->sourceLanguage => SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => 50
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        foreach (LANGUAGES as $language) {
            $query->filterWhere(['like', $language, $this->$language]);
        }

        return $dataProvider;
    }
}
