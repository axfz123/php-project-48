<?php

namespace Differ\Formatters;

use Differ\Formatters\Stylish;
use Differ\Formatters\Plain;
use Differ\Formatters\Json;

function formatDiff(array $diff, string $formatName): string
{
    return match ($formatName) {
        "stylish" => Stylish\formatToString($diff),
        "plain" => Plain\formatToString($diff),
        "json" => Json\formatToString($diff),
        default => throw new \Exception("Unsupported format '{$formatName}'"),
    };
}
