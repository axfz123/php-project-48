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
    return array_reduce(array_keys($tree), function ($acc, $name) use ($tree, $path) {
        $node = $tree[$name];
        if (isset($node['children'])) {
            $acc = array_merge($acc, getPlainDiff($node['children'], [...$path, $name]));
        } else {
            $value1 = array_key_exists('value-', $node) ? toString($node['value-']) : null;
            $value2 = array_key_exists('value+', $node) ? toString($node['value+']) : null;
            $pathStr = implode('.', [...$path, $name]);
            if (isset($value1) && !isset($value2)) {
                $acc[] = sprintf(REMOVED, $pathStr);
            }
            if (isset($value1) && isset($value2)) {
                $acc[] = sprintf(UPDATED, $pathStr, $value1, $value2);
            }
            if (!isset($value1) && isset($value2)) {
                $acc[] = sprintf(ADDED, $pathStr, $value2);
            }
        }
        return $acc;
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
