<?php

namespace Differ\Formatters\Stylish;

function formatToString(array $tree, int $depth = 1): string
{
    $result = [];
    foreach ($tree as $name => $node) {
        if (isset($node['children'])) {
            $value = formatToString($node['children'], $depth + 1);
            $result[] = formatValue($name, $value, $depth, ' ');
        } else {
            if (array_key_exists('value', $node)) {
                $result[] = formatValue($name, $node['value'], $depth, ' ');
            }
            if (array_key_exists('value-', $node)) {
                $result[] = formatValue($name, $node['value-'], $depth, '-');
            }
            if (array_key_exists('value+', $node)) {
                $result[] = formatValue($name, $node['value+'], $depth, '+');
            }
        }
    }
    $result[] = str_repeat(' ', ($depth - 1) * 4) . "}";
    return "{" . PHP_EOL . implode(PHP_EOL, $result);
}

function formatValue(string $name, mixed $value, int $depth, string $diff): string
{
    $valueStr = is_array($value) ? arrayToString($value, $depth + 1) : toString($value);
    $indent = str_repeat(' ', $depth * 4 - 2);
    return "{$indent}{$diff} {$name}: {$valueStr}";
}

function arrayToString(array $array, int $depth): string
{
    $indent = str_repeat(' ', $depth * 4);
    $result = array_map(function ($key, $value) use ($depth, $indent) {
        $valueStr = is_array($value) ? arrayToString($value, $depth + 1) : toString($value);
        return  "{$indent}{$key}: {$valueStr}";
    }, array_keys($array), $array);
    $result[] = str_repeat(' ', ($depth - 1) * 4) . "}";
    return "{" . PHP_EOL . implode(PHP_EOL, $result);
}

function toString(mixed $value): string
{
    $value = $value === null ? 'null' : $value;
    return trim(var_export($value, true), "'");
}
