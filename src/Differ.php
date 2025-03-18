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

    $fileArray1 = parseContent(getFileContents($realPath1), $ext1);
    $fileArray2 = parseContent(getFileContents($realPath2), $ext2);

    $compareArrays = function ($fileArray1, $fileArray2) use (&$compareArrays) {
        $result = [];
        $allKeys = array_unique(
            array_merge(
                array_keys($fileArray1),
                array_keys($fileArray2)
            )
        );
        sort($allKeys);

        foreach ($allKeys as $key) {
            $key1Exists = array_key_exists($key, $fileArray1);
            $key2Exists = array_key_exists($key, $fileArray2);

            $value1 = $key1Exists ? $fileArray1[$key] : null;
            $value2 = $key2Exists ? $fileArray2[$key] : null;

            $result[$key] = [];
            if ($value1 === $value2 && $key1Exists && $key2Exists) {
                $result[$key]['value'] = $value1;
            } else {
                if (is_array($value1) && is_array($value2)) {
                    $result[$key]['children'] = $compareArrays($value1, $value2);
                } else {
                    $key1Exists ? $result[$key]['value-'] = $value1 : null;
                    $key2Exists ? $result[$key]['value+'] = $value2 : null;
                }
            }
        }
        return $result;
    };

    $diff = $compareArrays($fileArray1, $fileArray2);

    return formatDiff($diff, $formatName);
}

function getFileContents($filePath)
{
    if (!file_exists($filePath)) {
        throw new \Exception("File \"{$filePath}\" not found");
    }
    return file_get_contents($filePath);
}
