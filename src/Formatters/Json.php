<?php

namespace Differ\Formatters\Json;

function formatToString(array $tree): string
{
    $str = json_encode($tree, JSON_PRETTY_PRINT);
    if ($str === false) {
        throw new \Exception("Failed to encode JSON");
    }
    return $str;
}
