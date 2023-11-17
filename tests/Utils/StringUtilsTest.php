<?php

use PHPUnit\Framework\TestCase;
use MediadataTv\Utils\StringUtils;

class StringUtilsTest extends TestCase
{


    public function testRemoveEmoji()
    {
        $hexes      = [
            '20',       // Space
            '41',       // Latin B capital letter
            '200D',     // Zero with Joiner
            '1F6B4',    // Bicycle emoji
            'FE0F',     // Variation selector-16
            '20',       // Space
            '42',       // Latin A capital letter
        ];
        $testString = StringUtils::unicodeHexToString($hexes);
        $this->assertEquals('A B', StringUtils::removeEmoji($testString, true));
        $this->assertNotEquals(' AB', StringUtils::removeEmoji($testString, true));
        $this->assertEquals(' A B', StringUtils::removeEmoji($testString, false));
        $this->assertNotEquals('AB', StringUtils::removeEmoji($testString, false));
    }

    public function testStartsWith()
    {
        $testCases = [
            ['string' => 'ABCDEF', 'starts' => 'ABC', 'result' => true],
            ['string' => 'ABC', 'starts' => 'ABC', 'result' => true],
            ['string' => 'AB', 'starts' => 'ABC', 'result' => false],
            ['string' => 'ABCDEF', 'starts' => 'aBC', 'result' => false],
            ['string' => 'AB', 'starts' => 'aBC', 'result' => false],
        ];
        foreach ($testCases as $case) {
            $this->assertEquals($case['result'], StringUtils::startsWith($case['string'], $case['starts']));
        }
    }

    public function testEndsWith()
    {
        $testCases = [
            ['string' => 'ABCDEF', 'ends' => 'DEF', 'result' => true],
            ['string' => 'ABC', 'ends' => 'ABC', 'result' => true],
            ['string' => 'AB', 'ends' => 'BC', 'result' => false],
            ['string' => 'ABCDEF', 'ends' => 'dEF', 'result' => false],
            ['string' => 'AB', 'ends' => 'aBC', 'result' => false],
        ];
        foreach ($testCases as $case) {
            $this->assertEquals($case['result'], StringUtils::endsWith($case['string'], $case['ends']));
        }
    }


}