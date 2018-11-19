<?php
namespace ale10257\translate\models;

class TService
{
	public static $terms = [];

	public static function t($message, $params = [])
	{
		$message = ModelTranslate::getMsg($message);
		$placeholders = [];
		if ($params) {
			foreach ($params as $name => $value) {
				$placeholders['{' . $name . '}'] = $value;
			}
		}
		return !$placeholders ? $message : strtr($message, $placeholders);
	}
}