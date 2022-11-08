<?php

namespace Blackdoor\Util;

/**
 * Static object for generating Time based One Time Passwords
 */
class TOTP
{
    /**
     * The time the OTP is valid in seconds
     *
     * @var integer
     */
    public static $validityDuration = 30;

    /**
     * Generates the time counter value based on the shared key
     *
     * @param integer $sharedKey
     * @param integer $timeOffset
     * @return int
     */
    public static function generateCounterValue(int $sharedKey, int $timeOffset = 0): int
    {
        return intval(((time() + $timeOffset) - $sharedKey) / self::$validityDuration);
    }

    /**
     * Generate a Time based One Time Password
     *
     * @param integer $sharedKey
     * @param integer $timeOffset
     * @return string
     */
    public static function generate(int $sharedKey, int $timeOffset = 0, string $hashAlgo = 'whirlpool'): string
    {
        return hash($hashAlgo, self::generateCounterValue($sharedKey, $timeOffset));
    }

    /**
     * Check if we can match the provided One Time Password
     *
     * @param string $otp
     * @param integer $sharedKey
     * @return boolean
     */
    public static function match(string $otp, int $sharedKey): bool
    {
        return in_array($otp, [
            self::generate($sharedKey, -self::$validityDuration),
            self::generate($sharedKey, 0),
        ], true);
    }

    /**
     * Generates a random shared integer key
     *
     * @return integer
     */
    public static function generatedSharedKey(): int
    {
        return random_int(random_int(1000, 10000), time() - 3600);
    }
}
