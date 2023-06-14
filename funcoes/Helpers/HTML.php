<?php

namespace Funcoes\Helpers;

class HTML
{
    public static function attrs(array $attrs = []): string
    {
        return implode(' ', array_map(function ($key) use ($attrs) {
            return "$key=\"{$attrs[$key]}\"";
        }, array_keys($attrs)));
    }

    public static function dataAttrs(array $attrs = []): string
    {
        return implode(' ', array_map(function ($key) use ($attrs) {
            $value = $attrs[$key];
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            return "data-$key=\"$value\"";
        }, array_keys($attrs)));
    }
}
