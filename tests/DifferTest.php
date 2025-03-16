<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\getFileContents;

class DifferTest extends TestCase
{
    public function testDifferJson(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $expected = getFileContents('tests/fixtures/expected-json.txt');
        $this->assertEquals($expected, $genDiffResult);
    }
    public function testDifferYaml(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml');
        $expected = getFileContents('tests/fixtures/expected-yaml.txt');
        $this->assertEquals($expected, $genDiffResult);
    }
    public function testGetNonExistentFile(): void
    {
        $this->expectException(\Exception::class);
        getFileContents('tests/fixtures/non-existent.txt');
    }
}
