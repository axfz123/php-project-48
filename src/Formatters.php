<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;
use Differ\Formatters\Json;

function formatDiff(array $diff, string $formatName): string
{
    return match ($formatName) {
        "stylish" => Stylish\formatToString($diff) . PHP_EOL,
        "plain" => Plain\formatToString($diff) . PHP_EOL,
        "json" => Json\formatToString($diff) . PHP_EOL,
        default => throw new \Exception("Unsupported formatter '{$formatName}'"),
    };
}
