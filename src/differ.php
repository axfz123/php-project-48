<?php

namespace Differ\Differ;

use function Differ\JsonParser\parseJson;

function genDiff(string $filePath1, string $filePath2): string
{
    $result = "{" . PHP_EOL;

    $jsonContent1 = parseJson($filePath1);
    $jsonContent2 = parseJson($filePath2);

    $allKeys = array_unique(
        array_merge(
            array_keys($jsonContent1),
            array_keys($jsonContent2)
        )
    );

    foreach ($allKeys as $key) {
        $value1 = array_key_exists($key, $jsonContent1) ? $jsonContent1[$key] : null;
        $value2 = array_key_exists($key, $jsonContent2) ? $jsonContent2[$key] : null;
        if ($value1 === $value2) {
            $result .= "    {$key}: {$value1}". PHP_EOL;
        } else {
            $result .= isset($value1) ? "  - {$key}: {$value1}" . PHP_EOL : "";
            $result .= isset($value2) ? "  + {$key}: {$value2}" . PHP_EOL : "";
        }
    }

    $result .= "}" . PHP_EOL;

    return $result;
}
