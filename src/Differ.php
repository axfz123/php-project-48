<?php

namespace Differ\Differ;

use Differ\Formatters;

use function Differ\Parsers\parseContent;

function genDiff(string $filePath1, string $filePath2, string $formatter = "stylish"): string
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

            if ($value1 === $value2 && $key1Exists && $key2Exists) {
                if (is_array($value1)) {
                    $result[] = [
                        'name' => $key,
                        'diff' => null,
                        'children' => $compareArrays($value1, $value2)
                    ];
                } else {
                    $result[] = [
                        'name' => $key,
                        'diff' => null,
                        'value' => $value1
                    ];
                }
            } else {
                if (is_array($value1) && is_array($value2)) {
                    $result[] = [
                        'name' => $key,
                        'diff' => null,
                        'children' => $compareArrays($value1, $value2)
                    ];
                } else {
                    if ($key1Exists) {
                        $result[] = [
                            'name' => $key,
                            'diff' => '-',
                            'value' => $value1
                        ];
                    }
                    if ($key2Exists) {
                        $result[] = [
                            'name' => $key,
                            'diff' => '+',
                            'value' => $value2
                        ];
                    }
                }
            }
        }
        return $result;
    };

    $comparedArrays = $compareArrays($fileArray1, $fileArray2);

    return match ($formatter) {
        "stylish" => Formatters\stylish($comparedArrays) . "\n",
        default => throw new \Exception("Unsupported formatter: \"{$formatter}\"")
    };
}

function getFileContents($filePath)
{
    if (! file_exists($filePath)) {
        throw new \Exception("File \"{$filePath}\" not found");
    }
    return file_get_contents($filePath);
}
