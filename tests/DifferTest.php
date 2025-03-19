<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\getFileContents;

class DifferTest extends TestCase
{
    public function testDifferJsonFormatterStylish(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'stylish');
        $expected = getFileContents('tests/fixtures/expected-json.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testDifferJsonFormatterPlain(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'plain');
        $expected = getFileContents('tests/fixtures/plain.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testDifferJsonFormatterJson(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'json');
        $expected = getFileContents('tests/fixtures/json.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testDifferYamlFormatterStylish(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'stylish');
        $expected = getFileContents('tests/fixtures/expected-yaml.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testDifferYamlFormatterPlain(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'plain');
        $expected = getFileContents('tests/fixtures/plain.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testDifferYamlFormatterJson(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml', 'json');
        $expected = getFileContents('tests/fixtures/json.txt');
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testGetNonExistentFile(): void
    {
        $this->expectException(\Exception::class);
        getFileContents('tests/fixtures/non-existent.txt');
    }
}
