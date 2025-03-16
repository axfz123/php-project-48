<?php

namespace Differ\Formatters;

function stylish(array $tree, int $depth = 1): string
{
    $indent = str_repeat(' ', $depth * 4 - 2);
    $result = array_map(function ($node) use ($depth, $indent) {
        $valueStr = isset($node['children']) ? stylish($node['children'], $depth + 1)
                    : (is_array($node['value']) ? arrayToString($node['value'], $depth + 1)
                        : toString($node['value']));
        $diff = $node['diff'] === null ? ' ' : $node['diff'];
        return "{$indent}{$diff} {$node['name']}: $valueStr";
    }, $tree);
    $result[] = str_repeat(' ', ($depth - 1) * 4) . "}";
    return "{\n" . implode("\n", $result);
}

function arrayToString(array $array, int $depth): string
{
    $indent = str_repeat(' ', $depth * 4);
    $result = array_map(function ($key, $value) use ($depth, $indent) {
        $valueStr = is_array($value) ? arrayToString($value, $depth + 1) : toString($value);
        return  "{$indent}{$key}: {$valueStr}";
    }, array_keys($array), $array);
    $result[] = str_repeat(' ', ($depth - 1) * 4) . "}";
    return "{\n" . implode("\n", $result);
}

function toString(mixed $value): string
{
    $value = $value === null ? 'null' : $value;
    return trim(var_export($value, true), "'");
}
