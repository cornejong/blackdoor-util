<?php

namespace Blackdoor\Util;

class Random
{
    public static function character($allowedCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        return substr(str_shuffle($allowedCharacters), 0, 1);
    }

    public static function int(int $min = 1, int $max = null)
    {
        return mt_rand($min, $max);
    }
}
