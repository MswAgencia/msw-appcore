<?php

namespace AppCore\Lib\Wrappers;

class Numbers {
	/**
	 * [currency description]
	 * @param  [type] $value    [description]
	 * @param  [type] $currency [description]
	 * @return [type]           [description]
	 */
	public static function currency($value, $currency) {
		$number = \Cake\I18n\Number::currency($value, $currency);

		$formattedValue = preg_split('/(R\$)/', $number);
		return 'R$ ' . $formattedValue[1];
	}
}