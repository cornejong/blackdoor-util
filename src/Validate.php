<?php

namespace Blackdoor\Util;

class Validate
{
    public const REQUIRE_URL_PATH = FILTER_FLAG_PATH_REQUIRED;

    public const URL_PATTERN = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
    public const GUID_PATTERN = "/(\{){0,1}[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}(\}){0,1}/";

    public const EMAIL_PATTERN = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/';
    public const EMAIL_PATTERN_COMPLEX = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

    /**
     * @param string $url
     * @param $flags
     */
    final public static function url(string $url, ...$flags): bool
    {
        return (filter_var($url, FILTER_VALIDATE_URL, ...$flags) !== false) ? true : false;
    }

    /**
     * @param string $url
     */
    final public static function urlSanitizer(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * @param \Traversable $urls
     * @param $result
     */
    final public static function urls(\Traversable $urls, &$result): bool
    {
        foreach ($urls as $index => $url) {
            $result[$index] = self::url($url);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * @param \Traversable $urls
     * @return mixed
     */
    final public static function urlsSanitizer(\Traversable $urls)
    {
        foreach ($urls as $index => $url) {
            $result[$index] = self::urlSanitizer($url);
        }

        return $result;
    }

    /**
     * @param string $email
     */
    final public static function email(string $email): bool
    {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) !== false) ? true : false;
    }

    /**
     * @param string $email
     */
    final public static function emailSanitizer(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * @param array $emails
     * @param $result
     */
    final public static function emails(array $emails, &$result): bool
    {
        foreach ($emails as $index => $email) {
            $result[$index] = self::email($email);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * @param \Traversable $emails
     * @return mixed
     */
    final public static function emailsSanitizer(\Traversable $emails)
    {
        foreach ($emails as $index => $email) {
            $result[$index] = self::emailSanitizer($email);
        }

        return $result;
    }

    /**
     * @param string $ip
     * @param bool $noPrivate
     */
    final public static function ip(string $ip, bool $noPrivate = true): bool
    {
        if ($noPrivate) {
            return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) ? true : false;
        }

        return (filter_var($ip, FILTER_VALIDATE_IP) !== false) ? true : false;
    }

    /**
     * @param \Traversable $ips
     * @param $result
     * @param $noPrivate
     */
    final public static function ips(\Traversable $ips, &$result, $noPrivate = true): bool
    {
        foreach ($ips as $index => $ip) {
            $result[$index] = self::ip($ip, $noPrivate);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * @param string $path
     */
    final public static function path(string $path): bool
    {
        return file_exists($path) ? true : false;
    }

    /**
     * @param string $path
     */
    final public static function isDirectory(string $path): bool
    {
        return is_dir($path) ? true : false;
    }

    /**
     * @param string $path
     */
    final public static function isFile(string $path): bool
    {
        return is_file($path) ? true : false;
    }

    /**
     * @param string $iban
     */
    final public static function iban(string $iban): bool
    {
        // Normalize input (remove spaces and make upcase)
        $iban = strtoupper(str_replace(' ', '', $iban));

        if (preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $iban)) {
            $country = substr($iban, 0, 2);
            $check = intval(substr($iban, 2, 2));
            $account = substr($iban, 4);

            // To numeric representation
            $search = range('A', 'Z');
            foreach (range(10, 35) as $tmp) {
                $replace[] = strval($tmp);
            }

            $numstr = str_replace($search, $replace, $account . $country . '00');

            // Calculate checksum
            $checksum = intval(substr($numstr, 0, 1));
            for ($pos = 1; $pos < strlen($numstr); $pos++) {
                $checksum *= 10;
                $checksum += intval(substr($numstr, $pos, 1));
                $checksum %= 97;
            }

            return ((98 - $checksum) == $check);
        } else {
            return false;
        }
    }

    /**
     * @param \Traversable $ibans
     * @param $result
     */
    final public static function ibans(\Traversable $ibans, &$result): bool
    {
        foreach ($ibans as $index => $iban) {
            $result[$index] = self::iban($iban);
        }

        return in_array(false, $result) ? false : true;
    }

    /**
     * Checks if the provided value is numeric
     *
     * @param $value
     * @return bool
     */
    public static function isNumber($value): bool
    {
        return is_numeric($value) ? true : false;
    }

    /**
     * Checks if the provided value is an integer
     *
     * @param $value
     * @return bool
     */
    public static function isInteger($value): bool
    {
        /* Make sure it's a numeric value */
        if (!Validate::isNumber($value)) {
            return false;
        }

        return preg_match('/^[\d]*$/', $value) ? true : false;
    }

    /**
     * Checks if the provided value is a float
     *
     * @param $value
     * @return bool
     */
    public static function isFloat($value): bool
    {
        /* Make sure it's a numeric value */
        if (!Validate::isNumber($value)) {
            return false;
        }

        return preg_match('/^[\d]*\.[\d]*$/', $value) ? true : false;
    }

    /**
     * Checks if the provided value is a valid guid
     *
     * @param mixed $value
     * @return bool
     */
    public static function guid($value): bool
    {
        return preg_match(self::GUID_PATTERN, $value);
    }

    public static function isBase64Encoded(string $value)
    {
        return preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value);
    }
}
