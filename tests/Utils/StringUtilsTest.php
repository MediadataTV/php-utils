<?php

use PHPUnit\Framework\TestCase;
use MediadataTv\Utils\StringUtils;

class StringUtilsTest extends TestCase
{

    private array $emojiTestCasesCodepoints = [];

    protected function setUp(): void
    {
        $emojiData  = file_get_contents('tests/data/StringUtils/emoji-test.txt');
        $lines      = explode("\n", $emojiData);
        $skipStatus = ['unqualified'];
        foreach ($lines as $line) {
            // Skip empty lines and comment lines

            if (empty($line) || $line[0] === '#') {
                continue;
            }

            // Extract the emoji and expected output from the line
            $parts        = explode(';', $line);
            $emojisArr    = explode(' ', trim($parts[0]));
            $descriptions = explode('#', $parts[1]);
            $status       = trim($descriptions[0]);
            $emoji        = trim(preg_replace('/E\d{1,}\.\d{1,}.*/u', '', trim($descriptions[1])));


            $codepoints = [];
            if (!in_array($status, $skipStatus, true)) {
                foreach ($emojisArr as $codepoint) {
                    $codepoints[] = trim($codepoint);
                }
            }
            $this->emojiTestCasesCodepoints[] = $codepoints;
        }
    }

    public function testRemoveAllEmojis()
    {
        foreach ($this->emojiTestCasesCodepoints as $codepoints) {
            $emoji  = StringUtils::unicodeHexToString($codepoints);
            $output = StringUtils::removeEmojisAndModifiers($emoji);
            $this->assertEquals('', $output, sprintf('Failed to remove emoji: %s [Codepoint: %s]', $emoji, implode(', ', $codepoints)));
        }
    }

    public function testRemoveEmojisLeaveStandardChars()
    {
        $testCases = [
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ'                   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'abcdefghijklmnopqrstuvwxyz'                   => 'abcdefghijklmnopqrstuvwxyz',
            '0123456789'                                   => '0123456789',
            'ºª·|!¡@#$%^&*()_-+={}:;¨<>?¿,./~\\\'"[]'      => 'ºª·|!¡@#$%^&*()_-+={}:;¨<>?¿,./~\\\'"[]',
            'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝÞßðÐ'             => 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝÞßðÐ',
            'àáâãäåæçèéêëìíîïñòóôõöøùúûüýþÿŒœĀāĆćĖėĢģŠšŦŧ' => 'àáâãäåæçèéêëìíîïñòóôõöøùúûüýþÿŒœĀāĆćĖėĢģŠšŦŧ',
            '01234567899️⃣0️⃣1️⃣2️⃣3️⃣4️⃣5️⃣6️⃣'           => '0123456789',
        ];
        foreach ($testCases as $input => $expected) {
            $output = StringUtils::removeEmojisAndModifiers($input);
            $this->assertEquals($expected, $output, sprintf('Error removing emojis: %s', $input));
        }
    }

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

    public function testUcFirstMultiByte()
    {
        $testCases = [
            'Abc Def Ghi'   => 'Abc def ghi',
            'Àbc Def Ghi'   => 'Àbc def ghi',
            'Abc DEF Ghì'   => 'Abc def ghì',
            'ABC DEF GHI'   => 'Abc def ghi',
            'àÌ'            => 'Àì',
            'Ì'             => 'Ì',
            ''              => '',
            '¡Abc Def Ghi!' => '¡Abc def ghi!',
            '!hellO!'       => '!Hello!',
            '!@#$abcDEF'    => '!@#$Abcdef',
            '123Ábc!@#$'    => '123Ábc!@#$',
            '123ábc!@#$'    => '123Ábc!@#$',
            '1(3Ábc!@#$'    => '1(3Ábc!@#$',
            '1(3ábc!@#$'    => '1(3Ábc!@#$',
            '-23Ábc!@#$'    => '-23Ábc!@#$',
            '-23ábc!@#$'    => '-23Ábc!@#$',
            '1É3Ábc!@#$'    => '1É3ábc!@#$',
            '1é3ábc!@#$'    => '1É3ábc!@#$',
            '!@#$'          => '!@#$',
            '!hE LlO!'      => '!He llo!',
            '!@#$abcD EF'   => '!@#$Abcd ef',
            '123abc!@#$'    => '123Abc!@#$',
            '1!@2#$3'       => '1!@2#$3',
        ];

        foreach ($testCases as $input => $result) {
            $this->assertEquals($result, StringUtils::ucfirstMultiByte($input), sprintf('Failed evaluating assert for: "%s"', $input));
        }
    }


    public function testCamelCaseToHyphen()
    {
        $testCases = [
            'helloWorld'        => 'hello-world',
            'thisIsATest'       => 'this-is-a-test',
            'singleWord'        => 'single-word',
            ''                  => '', // Edge case: empty string
            'alreadyHyphenated' => 'already-hyphenated',
            'noChange'          => 'no-change',
            '123Number'         => '123-number',      // Unexpected input: starts with a number
            'spaceSeparated'    => 'space-separated', // Edge case: space in the string
        ];

        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, StringUtils::camelCaseToHyphen($input));
        }
    }

    public function testUnderscoreToCamelCase()
    {
        $testCases = [
            'hello_world'        => 'helloWorld',
            'this_is_a_test'     => 'thisIsATest',
            'single_word'        => 'singleWord',
            ''                   => '', // Edge case: empty string
            'already_camel_case' => 'alreadyCamelCase',
            'no_change'          => 'noChange',
            '123_number'         => '123Number',      // Unexpected input: starts with a number
            'space_separated'    => 'spaceSeparated', // Edge case: space in the string
        ];

        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, StringUtils::underscoreToCamelCase($input));
        }
    }

    public function testHyphenToCamelCase()
    {
        $testCases = [
            'hello-world'        => 'helloWorld',
            'this-is-a-test'     => 'thisIsATest',
            'single-word'        => 'singleWord',
            ''                   => '', // Edge case: empty string
            'already-camel-case' => 'alreadyCamelCase',
            'no-change'          => 'noChange',
            '123-number'         => '123Number',      // Unexpected input: starts with a number
            'space-separated'    => 'spaceSeparated', // Edge case: space in the string
        ];

        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, StringUtils::hyphenToCamelCase($input));
        }
    }

    public function testCamelCaseToUnderscore()
    {
        $testCases = [
            'helloWorld'       => 'hello_world',
            'thisIsATest'      => 'this_is_a_test',
            'singleWord'       => 'single_word',
            ''                 => '', // Edge case: empty string
            'alreadyCamelCase' => 'already_camel_case',
            'noChange'         => 'no_change',
            '123Number'        => '123_number',      // Unexpected input: starts with a number
            'spaceSeparated'   => 'space_separated', // Edge case: space in the string
        ];

        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, StringUtils::camelCaseToUnderscore($input));
        }
    }

    public function testUtf8ToLatin1_Utf8String()
    {
        $utf8String = 'Cürdédrágön3:La malédiction du sorcier';
        $converted  = StringUtils::utf8ToLatin1($utf8String);

        // Convert result back to UTF-8 for comparison
        $convertedUtf8 = iconv('ISO-8859-1', 'UTF-8', $converted);

        $this->assertEquals($utf8String, $convertedUtf8);
        $this->assertTrue(mb_check_encoding($converted, 'ISO-8859-1'));
    }

    public function testUtf8ToLatin1_AlreadyLatin1()
    {
        $latin1String = 'Curdedragon3:La malediction du sorcier';
        $converted    = StringUtils::utf8ToLatin1($latin1String);

        $this->assertEquals($latin1String, $converted);
        $this->assertTrue(mb_check_encoding($converted, 'ISO-8859-1'));
    }

    public function testUtf8ToLatin1_EmojiOrUnconvertible()
    {
        $utf8String = 'Dragon 🐉';
        $converted  = StringUtils::utf8ToLatin1($utf8String);
        $this->assertEquals('Dragon', $converted); // Emoji removed
        $this->assertTrue(mb_check_encoding($converted, 'ISO-8859-1'));
    }


    public function testUtf8ToLatin1_IconvFailure()
    {
        // iconv fails if given invalid byte sequences (simulate that)
        // For this, use random invalid bytes (in real cases, this rarely happens)
        $invalidUtf8 = '\xC3\x28'; // Invalid 2-byte sequence
        $converted   = StringUtils::utf8ToLatin1($invalidUtf8);

        // Should return the original string
        $this->assertEquals($invalidUtf8, $converted);
    }

    public function testUtf8ToLatin1_EmptyString()
    {
        $empty     = '';
        $converted = StringUtils::utf8ToLatin1($empty);

        $this->assertSame('', $converted);
        $this->assertTrue(mb_check_encoding($converted, 'ISO-8859-1'));
    }

    public function testUtf8ToLatin1_SpecialMix()
    {
        $strings = [
            'YOURPRIVATELIBRARY「宗教に悩んだ時に読む本」'                                              => 'YOURPRIVATELIBRARY?????????????',
            'GRETELSMAGICALOVEN:SPIN-OFFDRAMAⅢ-PASTRYCHEFHANSELSTIME-TRAVELMAGIC'                     => 'GRETELSMAGICALOVEN:SPIN-OFFDRAMAIII-PASTRYCHEFHANSELSTIME-TRAVELMAGIC',
            'THE74THNHKREDANDWHITEYEAR-ENDSONGFESTIVALPART１'                                          => 'THE74THNHKREDANDWHITEYEAR-ENDSONGFESTIVALPART1',
            'NIGHTDRAMASERIAL　"ALOVESTORYINVIRTUALREALITY"EPS.2'                                      => 'NIGHTDRAMASERIAL "ALOVESTORYINVIRTUALREALITY"EPS.2',
            'NHKREGIONALSHOWCASES:＃TEREFUKU--FUKUOKAHUMANDOCUMENTARY'                                 => 'NHKREGIONALSHOWCASES:#TEREFUKU--FUKUOKAHUMANDOCUMENTARY',
            'NHKREGIONALSHOWCASES:＃TEREFUKU--FUKUOKAHUMANDOCUMENTARY(R)'                              => 'NHKREGIONALSHOWCASES:#TEREFUKU--FUKUOKAHUMANDOCUMENTARY(R)',
            'NIGHTDRAMASERIAL"HOSHISHINICHISSHORTSTORY:THEDEPARTMENTOFSUSTAINABLELIVING"「生活維持省」' => 'NIGHTDRAMASERIAL"HOSHISHINICHISSHORTSTORY:THEDEPARTMENTOFSUSTAINABLELIVING"???????',
            'ラーメンJAPAN15MIN.'                                                                     => '????JAPAN15MIN.',
            '＃SUPERMARKETLOVER"MEXICO"'                                                               => '#SUPERMARKETLOVER"MEXICO"',
        ];
        foreach ($strings as $input => $expected) {
            $converted = StringUtils::utf8ToLatin1($input);

            $this->assertSame($expected, $converted);
            $this->assertTrue(mb_check_encoding($converted, 'ISO-8859-1'));
        }
    }


}