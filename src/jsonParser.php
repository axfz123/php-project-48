<?php

namespace Differ\JsonParser;

function parseJson(string $file): array
{
    $fileContent = file_get_contents($file);
    return json_decode($fileContent, true);
}
