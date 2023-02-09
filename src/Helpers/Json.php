<?php

namespace App\Helpers;

class Json
{
    /**
     * @throws \JsonException
     */
    public static function encode(mixed $value): string
    {
        return \json_encode($value, \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }
}