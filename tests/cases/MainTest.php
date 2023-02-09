<?php

namespace tests\cases;

use App\Parsing\Parser;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    /**
     * @throws \App\Command\Parse\ParseError
     */
    public function testMain(): void
    {
        $expected = \file_get_contents(\dirname(__DIR__, 2) . '/task/output.json');
        $inputFileName = \dirname(__DIR__, 2) . '/task/input.csv';
        $actual = (new Parser($inputFileName))->run();
        static::assertJsonStringEqualsJsonString($expected, $actual);
    }
}