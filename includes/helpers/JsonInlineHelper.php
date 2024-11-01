<?php

namespace Yaglot\Helpers;

if (!defined('ABSPATH')) {
    exit;
}

class JsonInlineHelper {

    /**
     * @param string $string
     * @return string
     */
    public static function formatForApi($string) {

        $string = '"' . $string . '"';

        return json_decode(str_replace('\\/', '/', str_replace('\\\\', '\\', $string)));
    }


    /**
     * @param string $string
     * @return string
     */
    public static function unformatFromApi($string) {

        $string = str_replace('"', '', str_replace('/', '\\\\/', str_replace('\\u', '\\\\u', json_encode($string))));

        return $string;
    }
}
