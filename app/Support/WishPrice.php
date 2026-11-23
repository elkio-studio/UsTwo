<?php

namespace App\Support;

class WishPrice
{
    public const CURRENCIES = ['EUR'];

    public static function minor(mixed $value): ?int
    {
        if (! is_string($value) && ! is_int($value)) return null;
        if (! preg_match('/^(\d{1,8})(?:[.,](\d{1,2}))?$/D', trim((string) $value), $match)) return null;

        return (int) $match[1] * 100 + (int) str_pad($match[2] ?? '', 2, '0');
    }

    public static function decimal(?int $minor): ?string
    {
        return $minor === null ? null : intdiv($minor, 100).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }
}
