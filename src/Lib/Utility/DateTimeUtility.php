<?php

namespace AppCore\Lib\Utility;

class DateTimeUtility {

	/**
	 * [format description]
	 * @param  [type] $dateTime      [description]
	 * @param  [type] $currentFormat [description]
	 * @param  [type] $newFormat     [description]
	 * @return [type]                [description]
	 */
	public static function format($dateTime, $currentFormat, $newFormat) {
		$dateTime = \DateTime::createFromFormat($currentFormat, $dateTime);
		if(!empty($dateTime)):
			$formatedDateTime = strftime($newFormat, $dateTime->getTimestamp());
			if($formatedDateTime)
				return $formatedDateTime;
		endif;
		return false;
	}
}