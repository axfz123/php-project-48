<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;
use function Differ\Differ\getFileContents;

class DifferTest extends TestCase
{
    public static function additionProvider(): array
    {
        return [
            ['file1.json', 'file2.json', 'stylish', 'expected-json.txt'],
            ['file1.json', 'file2.json', 'plain', 'plain.txt'],
            ['file1.json', 'file2.json', 'json', 'json.txt'],
            ['file1.yml', 'file2.yaml', 'stylish', 'expected-yaml.txt'],
            ['file1.yml', 'file2.yaml', 'plain', 'plain.txt'],
            ['file1.yml', 'file2.yaml', 'json', 'json.txt'],
        ];
    }

    #[DataProvider('additionProvider')]
    public function testDiffer(string $file1, string $file2, string $formatName, string $expectedFile): void
    {
        $genDiffResult = genDiff("tests/fixtures/{$file1}", "tests/fixtures/{$file2}", $formatName);
        $expected = getFileContents("tests/fixtures/{$expectedFile}");
        $this->assertEquals($expected, $genDiffResult);
    }

    public function testGetNonExistentFile(): void
    {
        $this->expectException(\Exception::class);
        getFileContents('tests/fixtures/non-existent.txt');
    }
}
