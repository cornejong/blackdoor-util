<?php

/*
 * File: Expression.php
 * Project: Helpers
 * File Created: Saturday, 18th April 2020
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2020 - SouthCoast
 */

namespace Blackdoor\Util;

class Expression
{
    public const URL_PATTERN = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
    public const GUID_PATTERN = "/(\{){0,1}[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}(\}){0,1}/";

    public const EMAIL_PATTERN = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/';
    public const EMAIL_PATTERN_COMPLEX = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

    public static function match($expression, $subject, &$matches = null, $offset = 0): bool
    {
        return preg_match($expression, $subject, $matches);

        if ($matches === null) {
            return false;
        }

        if (is_string($matches[0])) {
            return true;
        }

        $matches = self::groupMatches($matches);

        return true;
    }

    public static function matchAll($expression, $subject, &$matches = null, $offset = 0): bool
    {
        return preg_match_all($expression, $subject, $matches);

        /* if ($matches === null) {
            $matches = [];
            return false;
        }

        if (is_string($matches[0])) {
            $matches = [];
            return true;
        } */

        // $matches = self::groupMatches($matches);

        /* if (count($matches) === 1) {
            $matches = $matches[0];
        } */

        return $result;
    }

    public static function matchAllGroup($expression, $subject, &$matches = null, $offset = 0)
    {
        return preg_match_all($expression, $subject, $matches, PREG_SET_ORDER);
    }

    public static function matchGroup(string $expression, $subject, &$matches = null, $offset = 0)
    {
        return preg_match($expression, $subject, $matches, PREG_SET_ORDER);
    }



    protected static function groupMatches(array $matches)
    {
        $result = [];

        foreach ($matches[0] as $i => $none) {
            foreach ($matches as $matchIndex => $matchValue) {
                $result[$i][$matchIndex] = $matchValue;
            }
        }

        return $result;
    }
}
