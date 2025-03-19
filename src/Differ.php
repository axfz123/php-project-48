<?php

namespace Differ\Differ;

use function Differ\Parsers\parseContent;
use function Differ\Formatters\formatDiff;

function genDiff(string $filePath1, string $filePath2, string $formatName = "stylish"): string
{
    $realPath1 = realpath($filePath1);
    $realPath2 = realpath($filePath2);

    $ext1 = pathinfo($realPath1, PATHINFO_EXTENSION);
    $ext2 = pathinfo($realPath2, PATHINFO_EXTENSION);

    $tree1 = parseContent(getFileContents($realPath1), $ext1);
    $tree2 = parseContent(getFileContents($realPath2), $ext2);

    $diff = compareArrays($tree1, $tree2);

    return formatDiff($diff, $formatName);
}

function compareArrays($array1, $array2)
{
    $allKeys = array_unique(
        array_merge(
            array_keys($array1),
            array_keys($array2)
        )
    );
    sort($allKeys);

    return array_reduce($allKeys, function ($acc, $key) use ($array1, $array2) {
        $key1Exists = array_key_exists($key, $array1);
        $key2Exists = array_key_exists($key, $array2);

        $value1 = $key1Exists ? $array1[$key] : null;
        $value2 = $key2Exists ? $array2[$key] : null;

        $acc[$key] = [];
        if ($value1 === $value2 && $key1Exists && $key2Exists) {
            $acc[$key]['value'] = $value1;
        } else {
            if (is_array($value1) && is_array($value2)) {
                $acc[$key]['children'] = compareArrays($value1, $value2);
            } else {
                $key1Exists ? $acc[$key]['value-'] = $value1 : null;
                $key2Exists ? $acc[$key]['value+'] = $value2 : null;
            }
        }
        return $acc;
    }, []);
}

function getFileContents($filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception("File \"{$filePath}\" not found");
    }
    return file_get_contents($filePath);
}
