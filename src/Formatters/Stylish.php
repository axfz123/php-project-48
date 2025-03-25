<?php

namespace Differ\Formatters\Stylish;

function formatToString(array $tree, int $depth = 1): string
{
    $items = array_reduce($tree, function (array $acc, array $node) use ($depth): array {
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
            'changed' => [
                formatValue($name, $node['value1'], $depth, '-'),
                formatValue($name, $node['value2'], $depth, '+'),
            ],
            default => throw new \Exception("Unsupported node type '{$node['type']}'"),
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
    $items = array_map(function (string $key, mixed $value) use ($depth, $indent): string {
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
    return match ($value) {
        null => 'null',
        default => trim(var_export($value, true), "'"),
    };
}
