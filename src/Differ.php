<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\parseContent;
use function Differ\Formatters\formatDiff;

function genDiff(string $filePath1, string $filePath2, string $formatName = "stylish"): string
{
    $ext1 = pathinfo($filePath1, PATHINFO_EXTENSION);
    $ext2 = pathinfo($filePath2, PATHINFO_EXTENSION);

    $tree1 = parseContent(getFileContents($filePath1), $ext1);
    $tree2 = parseContent(getFileContents($filePath2), $ext2);

    $diff = compareTrees($tree1, $tree2);

    return formatDiff($diff, $formatName);
}

function compareTrees(array $tree1, array $tree2): array
{
    $allKeys = array_unique(
        array_merge(
            array_keys($tree1),
            array_keys($tree2)
        )
    );
    $sortedAllKeys = sort($allKeys, fn($a, $b) => strcmp($a, $b));

    return array_map(function ($key) use ($tree1, $tree2) {
        $key1Exists = array_key_exists($key, $tree1);
        $key2Exists = array_key_exists($key, $tree2);

        $value1 = $key1Exists ? $tree1[$key] : null;
        $value2 = $key2Exists ? $tree2[$key] : null;

        if (is_array($value1) && is_array($value2)) {
            return [
                'name' => $key,
                'type' => 'nested',
                'children' => compareTrees($value1, $value2)
            ];
        }

        if (!$key1Exists) {
            return [
                'name' => $key,
                'type' => 'added',
                'value2' => $value2
            ];
        }

        if (!$key2Exists) {
            return [
                'name' => $key,
                'type' => 'removed',
                'value1' => $value1
            ];
        }

        if ($value1 === $value2) {
            return [
                'name' => $key,
                'type' => 'unchanged',
                'value1' => $value1
            ];
        }

        return [
            'name' => $key,
            'type' => 'changed',
            'value1' => $value1,
            'value2' => $value2
        ];
    }, $sortedAllKeys);
}

function getFileContents(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception("File \"{$filePath}\" not found");
    }

    $contents = file_get_contents($filePath);

    if ($contents === false) {
        throw new \Exception("File \"{$filePath}\" is empty");
    }

    return $contents;
}
