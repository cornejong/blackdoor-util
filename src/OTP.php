<?php

namespace Blackdoor\Util;

use Blackdoor\Util\Random;

/**
 * Static object for generating Time based One Time Passwords
 */
class OTP
{
    /**
     * Generate a One Time Password
     *
     * pattern syntax:
     *      0 = integer
     *      C = character (lower and upper case)
     *      A = uppercase character
     *      a = lowercase character
     *
     * @param string $pattern = '000 000'
     * @return string
     */
    public static function generate(string $pattern = '000 000'): string
    {
        $tokens = [
            '/0/' => function ($matches) {
                return rand(0, 9);
            },
            '/C/' => function ($matches) {
                return Random::character();
            },
            '/A/' => function ($matches) {
                return strtoupper(Random::character());
            },
            '/a/' => function ($matches) {
                return strtolower(Random::character());
            },
        ];

        return preg_replace_callback_array($tokens, $pattern);
    }
}
