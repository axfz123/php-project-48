<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testDiffer(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $expected = file_get_contents('tests/fixtures/file1-file2.txt');
        $this->assertEquals($genDiffResult, $expected);
    }
}
