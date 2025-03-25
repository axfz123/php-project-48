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

function parseJson(string $jsonString): array
{
    return json_decode(
        json: $jsonString,
        associative: true,
        flags: JSON_THROW_ON_ERROR
    );
}

function parseYaml(string $yamlString): array
{
    return Yaml::parse($yamlString) ?? [];
}
