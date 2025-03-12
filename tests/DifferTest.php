<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    private function getFileContents($filePath)
    {
        if (! file_exists($filePath)) {
            throw new \Exception("File \"{$filePath}\" not found");
        }
        return file_get_contents($filePath);
    }

    public function testDifferJson(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $expected = $this->getFileContents('tests/fixtures/expected-json.txt');
        $this->assertEquals($expected, $genDiffResult);
    }
    public function testDifferYaml(): void
    {
        $genDiffResult = genDiff('tests/fixtures/file1.yml', 'tests/fixtures/file2.yaml');
        $expected = $this->getFileContents('tests/fixtures/expected-yaml.txt');
        $this->assertEquals($expected, $genDiffResult);
    }
}
