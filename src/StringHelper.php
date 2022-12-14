<?php

namespace Blackdoor\Util;

use Error;

class StringHelper
{
    /**
     * @param string $needle
     * @param string $string
     */
    public static function contains(string $needle, string $string)
    {
        return strpos($string, $needle) !== false ? true : false;
    }

    /**
     * @param string $needle
     * @param string $string
     * @return mixed
     */
    public static function startsWith(string $needle, string $string)
    {
        return $needle === substr($string, 0, strlen($needle)) ? true : false;
    }

    /**
     * @param string $needle
     * @param string $string
     * @return mixed
     */
    public static function endsWith(string $needle, string $string)
    {
        return $needle === substr($string, -strlen($needle), strlen($needle)) ? true : false;
    }

    /**
     * @param $data
     */
    public function stringify($data)
    {
        switch (gettype($data)) {
            case 'string':
                return (string) $data;
                break;

            case 'integer':
            case 'double':
                return '' . $data . '';
                break;

            case 'boolean':
                return (string) ($data) ? 'true' : false;
                break;

            case 'NULL':
                return 'NULL';
                break;

            case 'array':
            case 'object':
                return strval($data);
                break;

            default:
                throw new Error('Unsupported Type for to string conversion! Provided Type: ' . gettype($data), 1);
                break;
        }
    }

    /**
     * @param string $string
     * @return mixed
     */
    public static function explodeCamelCase(string $string): array
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $string, $matches);
        return $matches[0];
    }

    /**
     * removes all special characters from a given string
     *
     * @param string $text
     * @return string
     */
    public static function clean(string $text): string
    {
        $utf8 = [
            '/[????????????]/u' => 'a',
            '/[??????????]/u' => 'A',
            '/[????????]/u' => 'I',
            '/[????????]/u' => 'i',
            '/[????????]/u' => 'e',
            '/[????????]/u' => 'E',
            '/[????????????]/u' => 'o',
            '/[??????????]/u' => 'O',
            '/[????????]/u' => 'u',
            '/[????????]/u' => 'U',
            '/??/' => 'c',
            '/??/' => 'C',
            '/??/' => 'n',
            '/??/' => 'N',
            '/???/' => '-', // UTF-8 hyphen to "normal" hyphen
            '/[???????????????]/u' => ' ', // Literally a single quote
            '/[?????????????]/u' => ' ', // Double quote
            '/ /' => ' ', // nonbreaking space (equiv. to 0x160)
        ];

        $result = preg_replace(array_keys($utf8), array_values($utf8), $text);

        return $result ?? $text;
    }

    public function remove_accent($str)
    {
        $a = array('??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??', '??');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }


    /**
     * Converts a stringified value into its correct type
     *
     * @param string $string
     */
    public static function getRealType(string $string)
    {
        if (Number::isFloat($string)) {
            return (float) Number::convert2Float($string);
        }

        if (Number::isInteger($string)) {
            return (int) Number::convert2Integer($string);
        }

        if (strtolower($string) === 'true') {
            return true;
        }

        if (strtolower($string) === 'false') {
            return false;
        }

        if (strtolower($string) === 'null') {
            return null;
        }

        return $string;
    }

    public static function pad(int $totalLength, string $string): string
    {
        $stringLength = strlen($string);

        if ($stringLength >= $totalLength) {
            return $string;
        }

        $missing = ($totalLength - $stringLength);

        while ($missing >= 0) {
            $string = ' ' . $string;
            $missing--;
        }

        return $string;
    }
}
