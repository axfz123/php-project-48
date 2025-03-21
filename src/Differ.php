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

    $diff = compareArrays($tree1, $tree2);

    //echo formatDiff($diff, "json"); die;

    return formatDiff($diff, $formatName);
}

function compareArrays(array $array1, array $array2): array
{
    $allKeys = array_unique(
        array_merge(
            array_keys($array1),
            array_keys($array2)
        )
    );
    $sortedAllKeys = sort($allKeys, fn($a, $b) => strcmp($a, $b));

    return array_map(function ($key) use ($array1, $array2) {
        $key1Exists = array_key_exists($key, $array1);
        $key2Exists = array_key_exists($key, $array2);

        $value1 = $key1Exists ? $array1[$key] : null;
        $value2 = $key2Exists ? $array2[$key] : null;

        $type = match (true) {
            $value1 === $value2 && $key1Exists && $key2Exists => 'unchanged',
            is_array($value1) && is_array($value2) => 'nested',
            !$key1Exists => 'added',
            !$key2Exists => 'removed',
            default => 'changed',
        };

        $children = $type === 'nested' ? compareArrays($value1, $value2) : null;

        return [
            'name' => $key,
            'type' => $type,
            'value1' => $value1,
            'value2' => $value2,
            'children' => $children
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
