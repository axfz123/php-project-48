<?php

namespace Differ\Formatters\Plain;

const REMOVED = "Property '%s' was removed";
const UPDATED = "Property '%s' was updated. From %s to %s";
const ADDED = "Property '%s' was added with value: %s";
const COMPLEX_VALUE = "[complex value]";

function formatToString(array $tree): string
{
    return implode(PHP_EOL, getPlainDiff($tree));
}

function getPlainDiff(array $tree, array $path = []): array
{
    return array_reduce($tree, function ($acc, $node) use ($path) {
        $name = $node['name'];
        $currentPath = [...$path, $name];
        $pathStr = implode(".", $currentPath);
        $result = match ($node['type']) {
            'nested' => getPlainDiff($node['children'], $currentPath),
            'removed' => [sprintf(REMOVED, $pathStr)],
            'added' => [sprintf(ADDED, $pathStr, toString($node['value2']))],
            'changed' => [sprintf(UPDATED, $pathStr, toString($node['value1']), toString($node['value2']))],
            default => [],
        };
        return array_merge($acc, $result);
    }, []);
}

function toString(mixed $value): string
{
    return match (true) {
        $value === null => 'null',
        $value === true => 'true',
        $value === false => 'false',
        is_numeric($value) => strval($value),
        is_array($value) => COMPLEX_VALUE,
        default => "'" . trim(var_export($value, true), "'") . "'",
    };
}
