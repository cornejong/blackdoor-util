<?php

namespace Blackdoor\Util;

class TimeInSeconds
{
    public const MINUTE =      60;
    public const HOUR =        3600;
    public const DAY =         86400;
    public const WEEK =        604800;
    public const MONTH =       2678400;
    public const YEAR =        31556926;
    public const CENTURY =     100 * TimeInSeconds::YEAR;
    public const MILLENNIUM =  1000 * TimeInSeconds::YEAR;

    /**
     * Converts the plain text query in seconds.
     *
     * Example:
     *      TimeInSeconds::get('4 days, 23 hours, 48 minutes, 33 seconds');
     *      TimeInSeconds::get('12:14');
     *
     * @param string $query     The plain text query separated by comma's
     * @return int              The time in seconds
     */
    public static function get(string $query): int
    {
        /* Check if it contains the separator */
        if (strpos($query, ',') === false) {
            /* If not, just wrap it in an array */
            $query_array = [$query];
        } else {
            /* else, Explode it by the comma */
            $query_array = explode(',', $query);
        }

        /* Set the total time to 0 */
        $total = 0;

        /* Loop over the entries */
        foreach ($query_array as $partial) {
            /* Add the wanted period to the current time and subtract the current timestamp  */
            /* This will result in just the time in seconds for the provided period */
            $total += abs(strtotime('now +' . trim($partial)) - time());
        }

        /* Return the total */
        return $total;
    }

    /**
     * Calculates the time in seconds in the provided range
     *
     * Example:
     *      TimeInSeconds::between('12:00', '12:01');
     *      TimeInSeconds::between('2019-01-01 12:00', '2019-01-04 12:01');
     *      TimeInSeconds::between(new DateTime($dateTime_a), new DateTime($dateTime_b));
     *
     * @param mixed $from      The start of the period
     * @param mixed $to        The end of the period
     * @return int             The time in seconds between the range
     */
    public static function between($from, $to = null): int
    {
        /* Lets create an array for convenience  */
        $date = ['to' => $to ?? time(), 'from' => $from];

        /* Loop over the dates */
        foreach ($date as $type => &$value) {
            /* Check if it's a DateTime Object */
            if ($value instanceof DateTime) {
                /* Get it's timestamp */
                $value = $value->getTimestamp();
                continue;
            }

            if (is_int($value)) {
                continue;
            }

            /* Check if its just a time instead of a full date */
            if (preg_match('/^([0-2][0-9]\:[0-5][0-9])(\:[0-5][0-9]|)$/', $value, $matches)) {
                /* If it is, add the current date to it */
                $value = date('Y-m-d') . ' ' . $value;
            }

            /* Create a timestamp from it */
            $value = strtotime($value);
        }

        /* Return the time in between */
        return abs(intval($date['to']) - intval($date['from']));
    }

    public static function forHumansShort(int $timeInSeconds): string
    {
        $bit = [
            'y' => $timeInSeconds / self::YEAR % 12,
            'w' => $timeInSeconds / self::WEEK % 52,
            'd' => $timeInSeconds / self::DAY % 7,
            'h' => $timeInSeconds / self::HOUR % 24,
            'm' => $timeInSeconds / self::MINUTE % 60,
            's' => $timeInSeconds % 60
        ];

        foreach ($bit as $label => $value) {
            if ($value > 0) {
                $output[] = $value . $label;
            }
        }

        return join(' ', $output);
    }

    public static function forHumans(int $timeInSeconds)
    {
        $bit = [
            ' year' => $timeInSeconds / self::YEAR % 12,
            ' week' => $timeInSeconds / self::WEEK % 52,
            ' day' => $timeInSeconds / self::DAY % 7,
            ' hour' => $timeInSeconds / self::HOUR % 24,
            ' minute' => $timeInSeconds / self::MINUTE % 60,
            ' second' => $timeInSeconds % 60
        ];

        foreach ($bit as $label => $value) {
            if ($value > 1) {
                $output[] = $value . $label . 's';
            }

            if ($value == 1) {
                $output[] = $value . $label;
            }
        }

        array_splice($output, count($output) - 1, 0, 'and');

        return join(' ', $output);
    }

    public static function betweenForHumans($from, $to = null, bool $short = false)
    {
        $between = self::between($from, $to);
        return $short ? self::forHumansShort($between) : self::forHumans($between);
    }

    public static function betweenForHumansSimple($from, $to = null)
    {
        $between = self::between($from, $to);
        return self::forHumansSimple($between);
    }

    public static function forHumansSimple_old(int $timeInSeconds)
    {
        var_dump($timeInSeconds);
        die;

        // Make sure the timestamp to check is in the past.
        if ($timeInSeconds > time()) {
            throw new Exception('Time is in the future');
        }

        $units = [
            'second' => self::MINUTE,
            'minute' => self::HOUR,
            'hour' => self::DAY,
            'day' => self::MONTH,
            'week' => self::YEAR
        ];

        $label = 'year';
        $value = null;

        foreach ($units as $unitLabel => $unitValue) {
            if ($timeInSeconds < $unitValue) {
                $label = $unitLabel;
                $value = round($timeInSeconds / $unitValue);
                break;
            }
        }

        if (!$value) {
            return 'a while';
        }

        return $value <= 1
            ? sprintf('a %s', $label)
            : sprintf('%s %ss', $value, $label);
    }

    public static function forHumansSimple(int $timeInSeconds)
    {
        $unit = [
            'year' => $timeInSeconds / self::YEAR % 12,
            'week' => $timeInSeconds / self::WEEK % 52,
            'day' => $timeInSeconds / self::DAY % 7,
            'hour' => $timeInSeconds / self::HOUR % 24,
            'minute' => $timeInSeconds / self::MINUTE % 60,
            'second' => $timeInSeconds % 60
        ];

        foreach ($unit as $label => $value) {
            if ($value === 0) {
                continue;
            }

            if ($label === 'day' && $value === 1) {
                return 'yesterday';
            }

            return $value === 1
                ? sprintf('a %s', $label)
                : sprintf('%s %ss', $value, $label);
        }
    }
}
