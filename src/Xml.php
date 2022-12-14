<?php

namespace Blackdoor\Util;

use XMLReader;
use Blackdoor\Util\StringHelper;
use Blackdoor\Util\Objects\XmlObject;

class Xml
{
    public const HTTP_MIME = 'application/xml';

    /**
     * @param string $xml
     * @return mixed
     */
    public static function isValid(string $xml): bool
    {
        $reader = new XMLReader();
        $reader->XML($xml);

        $valid = $reader->isValid();

        $reader->close();
        unset($reader);

        return $valid;
    }

    /**
     * @param string $openingTag
     * @param array $data
     * @param $version
     */
    public static function stringify(string $openingTag, array $data, $version = '1.0'): string
    {
        return (new XmlObject($openingTag, $version))->loadArray($data)->getXml();
    }

    /**
     * @param string $openingTag
     * @param array $data
     * @param $version
     */
    public static function encode(string $openingTag, array $data, $version = '1.0'): string
    {
        return self::stringify($openingTag, $data, $version);
    }

    /**
     * @param string $string
     * @param $array
     */
    public static function parse(string $string, $array = true)
    {
        return ($array) ? self::parseToArray($string) : self::parseToObject($string);
    }

    /**
     * @param string $data
     */
    public static function parseToArray(string $data)
    {
        $array = simplexml_load_string($data);

        return ArrayHelper::sanitize($array);
    }

    /**
     * @param string $data
     */
    public static function parseToObject(string $data)
    {
        $array = simplexml_load_string($data);
        $array = ArrayHelper::sanitize($array);

        return ArrayHelper::objectify($array);
    }

    /**
     * Removes all the '@' symbols from the attributes keys
     *
     * @param array $array
     * @return array
     */
    public static function cleanup(array $array): array
    {
        $tmp = [];
        foreach ($array as $key => $element) {
            if (StringHelper::startsWith('@', $key)) {
                $key = ltrim($key, '@');
            }

            $tmp[$key] = (is_array($element)) ? self::cleanup($element) : $element;
        }

        return $tmp;
    }
}
