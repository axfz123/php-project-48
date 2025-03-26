<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseContent(string $content, string $format): array
{
    return match ($format) {
        'json' => parseJson($content),
        'yaml', 'yml' => parseYaml($content),
        default => throw new \Exception("Unsupported format: \"{$format}\"")
    };
}

function parseJson(string $str): array
{
    return json_decode(
        json: $str,
        associative: true,
        flags: JSON_THROW_ON_ERROR
    );
}

function parseYaml(string $str): array
{
    return Yaml::parse($str, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
}
