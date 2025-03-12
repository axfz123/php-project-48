<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

function parseContent(string $content, string $ext): array
{
    return match ($ext) {
        'json' => parseJson($content),
        'yaml', 'yml' => parseYaml($content),
        default => throw new \Exception("Unsupported file extension: \"{$ext}\"")
    };
}

function parseJson(string $jsonString): array
{
    return json_decode($jsonString, true) ?? [];
}

function parseYaml(string $yamlString): array
{
    try {
        return Yaml::parse($yamlString) ?? [];
    } catch (ParseException $exception) {
        printf("Unable to parse the YAML string: %s", $exception->getMessage());
        return [];
    }
}
