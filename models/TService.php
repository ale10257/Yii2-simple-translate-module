<?php
namespace ale10257\translate\models;

class TService
{
	public static $terms = [];

	public static function t($message, $params = [])
	{
	    $model = new ModelTranslate();
		$message = $model->getMsg($message);
		$placeholders = [];
		if ($params) {
			foreach ($params as $name => $value) {
				$placeholders['{' . $name . '}'] = $value;
			}
		}
		return !$placeholders ? $message : strtr($message, $placeholders);
	}
}