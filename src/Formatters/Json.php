<?php

namespace Differ\Formatters\Json;

/**
 * @throws \JsonException
 */
function formatToString(array $tree): string
{
    return json_encode(
        value: $tree,
        flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
    );
}
