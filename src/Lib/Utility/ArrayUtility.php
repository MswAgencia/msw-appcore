<?php


namespace AppCore\Lib\Utility;

use Cake\Utility\Hash;

/**
 * Class ArrayUtility
 * @package AppCore\Lib\Utility
 */
class ArrayUtility {

    /**
     * @param array $array
     * @param $key
     * @param string $mark
     * @return array
     */
    public static function markValue(array $array, $key, $mark = ''){
        if(is_array($key)) {
            foreach($key as $k) {
                if(isset($array[$k]))
                    $array[$k] = $array[$k] . ' ' . $mark;
            }
        }
        else {
            if(isset($array[$key]))
                $array[$key] = $array[$key] . ' ' . $mark;
        }

        return $array;
    }
} 