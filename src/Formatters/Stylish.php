<?php

namespace Differ\Formatters\Stylish;

function formatToString(array $tree, int $depth = 1): string
{
    $items = array_reduce($tree, function ($acc, $node) use ($depth) {
        $name = $node['name'];
        $result = match ($node['type']) {
            'nested' => [
                formatValue($name, formatToString($node['children'], $depth + 1), $depth, ' ')
            ],
            'unchanged' => [
                formatValue($name, $node['value1'], $depth, ' ')
            ],
            'removed' => [
                formatValue($name, $node['value1'], $depth, '-')
            ],
            'added' => [
                formatValue($name, $node['value2'], $depth, '+')
            ],
            default => [ // changed
                formatValue($name, $node['value1'], $depth, '-'),
                formatValue($name, $node['value2'], $depth, '+'),
            ],
        };
        return array_merge($acc, $result);
    }, []);

    $lastIndent = str_repeat(' ', ($depth - 1) * 4);
    $result = [
        "{",
        ...$items,
        "{$lastIndent}}",
    ];

    return implode(PHP_EOL, $result);
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
    $items = array_map(function ($key, $value) use ($depth, $indent) {
        $valueStr = is_array($value) ? arrayToString($value, $depth + 1) : toString($value);
        return  "{$indent}{$key}: {$valueStr}";
    }, array_keys($array), $array);

    $lastIndent = str_repeat(' ', ($depth - 1) * 4);
    $result = [
        "{",
        ...$items,
        "{$lastIndent}}",
    ];

    return implode(PHP_EOL, $result);
}

function toString(mixed $value): string
{
    $value = $value === null ? 'null' : $value;
    return trim(var_export($value, true), "'");
}
