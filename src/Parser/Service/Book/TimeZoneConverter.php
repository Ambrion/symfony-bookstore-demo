<?php

namespace App\Parser\Service\Book;

class TimeZoneConverter
{
    /**
     * @var array<string, string>
     */
    private static array $timezoneMap = [
        '-12:00' => 'Etc/GMT+12',
        '-11:00' => 'Pacific/Midway',
        '-10:00' => 'Pacific/Honolulu',
        '-09:00' => 'America/Anchorage',
        '-08:00' => 'America/Los_Angeles',
        '-07:00' => 'America/Denver',
        '-06:00' => 'America/Chicago',
        '-05:00' => 'America/New_York',
        '-04:00' => 'America/Halifax',
        '-03:00' => 'America/Sao_Paulo',
        '-02:00' => 'Atlantic/South_Georgia',
        '-01:00' => 'Atlantic/Azores',
        '+00:00' => 'Europe/London',
        '+01:00' => 'Europe/Paris',
        '+02:00' => 'Europe/Berlin',
        '+03:00' => 'Europe/Moscow',
        '+04:00' => 'Asia/Dubai',
        '+05:00' => 'Asia/Karachi',
        '+06:00' => 'Asia/Dhaka',
        '+07:00' => 'Asia/Bangkok',
        '+08:00' => 'Asia/Shanghai',
        '+09:00' => 'Asia/Tokyo',
        '+10:00' => 'Australia/Sydney',
        '+11:00' => 'Pacific/Noumea',
        '+12:00' => 'Pacific/Auckland',
        '+13:00' => 'Pacific/Tongatapu',
        '+14:00' => 'Pacific/Kiritimati',
    ];

    public static function offsetToTimezoneName(string $offset): string
    {
        $normalizedOffset = self::normalizeOffset($offset);

        return self::$timezoneMap[$normalizedOffset] ?? 'UTC';
    }

    private static function normalizeOffset(string $offset): string
    {
        // Handle cases like "-0800" -> "-08:00"
        if (preg_match('/^([+-])(\d{2})(\d{2})$/', $offset, $matches)) {
            return sprintf('%s%s:%s', $matches[1], $matches[2], $matches[3]);
        }

        // Handle cases like "-08:00"
        if (preg_match('/^([+-]\d{2}):\d{2}$/', $offset)) {
            return $offset;
        }

        // Handle cases like "+0" -> "+00:00"
        if (preg_match('/^[+-]\d$/', $offset)) {
            $numericOffset = (int) $offset;

            return sprintf('%s0%s:00', $numericOffset < 0 ? '-' : '+', abs($numericOffset));
        }

        // Default case
        return '+00:00';
    }
}
