<?php

namespace Differ\Differ;

use function Differ\Parsers\parseContent;

function genDiff(string $filePath1, string $filePath2): string
{
    $realPath1 = realpath($filePath1);
    $realPath2 = realpath($filePath2);

    $ext1 = pathinfo($realPath1, PATHINFO_EXTENSION);
    $ext2 = pathinfo($realPath2, PATHINFO_EXTENSION);

    $fileArray1 = parseContent(getFileContents($realPath1), $ext1);
    $fileArray2 = parseContent(getFileContents($realPath2), $ext2);

    $result = "{\n";

    $allKeys = array_unique(
        array_merge(
            array_keys($fileArray1),
            array_keys($fileArray2)
        )
    );

    foreach ($allKeys as $key) {
        $value1 = array_key_exists($key, $fileArray1) ? $fileArray1[$key] : null;
        $value2 = array_key_exists($key, $fileArray2) ? $fileArray2[$key] : null;
        if ($value1 === $value2) {
            $result .= "    {$key}: {$value1}\n";
        } else {
            $result .= isset($value1) ? "  - {$key}: {$value1}\n" : "";
            $result .= isset($value2) ? "  + {$key}: {$value2}\n" : "";
        }
    }

    $result .= "}\n";

    return $result;
}

function getFileContents($filePath)
{
    if (! file_exists($filePath)) {
        throw new \Exception("File \"{$filePath}\" not found");
    }
    return file_get_contents($filePath);
}
