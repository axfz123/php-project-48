<?php

namespace Differ\Formatters\Plain;

const STR_REMOVED = "Property '%s' was removed";
const STR_UPDATED = "Property '%s' was updated. From %s to %s";
const STR_ADDED = "Property '%s' was added with value: %s";
const STR_COMPLEX_VALUE = "[complex value]";

function formatToString(array $tree): string
{
    return implode(PHP_EOL, getPlainDiff($tree));
}

function getPlainDiff(array $tree, array $pathItems = []): array
{
    return array_reduce($tree, function (array $acc, array $node) use ($pathItems): array {
        $name = $node['name'];
        $currentPathItems = [...$pathItems, $name];
        $pathStr = implode(".", $currentPathItems);
        return match ($node['type']) {
            'nested' => [
                ...$acc,
                ...getPlainDiff($node['children'], $currentPathItems)
            ],
            'added' => [
                ...$acc,
                sprintf(STR_ADDED, $pathStr, toString($node['value2']))
            ],
            'removed' => [
                ...$acc,
                sprintf(STR_REMOVED, $pathStr)
            ],
            'changed' => [
                ...$acc,
                sprintf(STR_UPDATED, $pathStr, toString($node['value1']), toString($node['value2']))
            ],
            'unchanged' => $acc,
            default => throw new \Exception("Unsupported node type '{$node['type']}'"),
        };
    }, []);
}

function toString(mixed $value): string
{
    return match (true) {
        $value === null => 'null',
        $value === true => 'true',
        $value === false => 'false',
        is_numeric($value) => strval($value),
        is_array($value) => STR_COMPLEX_VALUE,
        default => var_export($value, true),
    };
}
