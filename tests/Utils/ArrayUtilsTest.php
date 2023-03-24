<?php

use PHPUnit\Framework\TestCase;
use MediadataTv\Utils\ArrayUtils;
use MediadataTv\Utils\StringUtils;

class ArrayUtilsTest extends TestCase
{


    protected $testArray = [
        'plain_null'         => null,
        'plain_int'          => 100,
        'plain_string'       => 'string',
        'plain_empty_string' => '',
        'nested_array'       => [
            'nested value 0',
            'nested value 1',
            'nested value 2',
        ],
        'nested_hash'        => [
            'nested_hash_0' => [
                'nested_hash_0_0' => 'value 0_0',
                'nested_hash_0_1' => 'value 0_1',
                'nested_hash_0_2' => 'value 0_2',
            ],
            'nested_hash_1' => [
                'nested_hash_1_0' => 'value 1_0',
                'nested_hash_1_1' => 'value 1_1',
            ],
            'nested_hash_2' => [
                'nested_hash_2_0' => 'value 2_0',
                'nested_hash_2_1' => 'value 2_1',
                'nested_hash_2_2' => 'value 2_2',
                'nested_hash_2_3' => 'value 2_3',
                'nested_hash_2_4' => 'value 2_4',
            ],
        ],
        'prices'             => [
            [
                'code'   => 'ES',
                'active' => true,
                'price'  => '9.99 EUR',
            ],
            [
                'code'   => 'GB',
                'active' => false,
                'price'  => '9.99 GBP',
            ],
            [
                'code'   => 'US',
                'active' => false,
                'price'  => '9.99 USD',
            ],
        ],
    ];

    public function testGetValueNotExists(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists', '/', ''), '');
        $this->assertFalse(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists', '/', false));
        $this->assertNotFalse(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists'));
        $this->assertNotSame('', ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists'));
        $this->assertNotNull(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists', '/', ''));
        $this->assertNotNull(ArrayUtils::getNestedArrayValue($copyArray, 'index_does_not_exists', '/', false));
    }

    protected function getCopyTestArray(): array
    {
        return $this->testArray;
    }

    public function testGetValueIsNull(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'plain_null'));
        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'plain_null', '/', ''));
        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'plain_null', '/', false));
    }

    public function testGetKeyFilter(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=ES]/price'), '9.99 EUR');
        $this->assertNotSame('9.99 EUR', ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=IT]/price'));
        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=IT]/price'));
    }

    public function testSetValuePlainInt(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'plain_int'), 100);
        ArrayUtils::setNestedArrayValue($copyArray, 'plain_int', 200);
        $this->assertNotSame(100, ArrayUtils::getNestedArrayValue($copyArray, 'plain_int'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'plain_int'), 200);
    }

    public function testSetValuePlainString(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'plain_string'), 'string');
        ArrayUtils::setNestedArrayValue($copyArray, 'plain_string', 'new string');
        $this->assertNotSame('string', ArrayUtils::getNestedArrayValue($copyArray, 'plain_string'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'plain_string'), 'new string');
    }


    public function testSetValueNestedArray(): void
    {
        $copyArray = $this->getCopyTestArray();

//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/0'), 'nested value 0');
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/1'), 'nested value 1');
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/2'), 'nested value 2');
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/3'), null);
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/4'), null);
        $this->assertCount(3, ArrayUtils::getNestedArrayValue($copyArray, 'nested_array'));
        ArrayUtils::setNestedArrayValue($copyArray, 'nested_array/1', 'new nested value 1');
        ArrayUtils::setNestedArrayValue($copyArray, 'nested_array/4', 'new nested value 4');
        $this->assertNotSame('nested value 0', ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/1'));
        $this->assertNotSame('new nested value 4', ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/3'));
        $this->assertNotNull(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/4'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/0'), 'nested value 0');
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/1'), 'new nested value 1');
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/2'), 'nested value 2');
        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/3'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_array/4'), 'new nested value 4');
        $this->assertCount(4, ArrayUtils::getNestedArrayValue($copyArray, 'nested_array'));

    }

    public function testSetValueNestedHashArray(): void
    {
        $copyArray = $this->getCopyTestArray();

//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_0/nested_hash_0_1'), 'value 0_1');
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_1'), 'value 1_1');
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_2'), null);
//        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_2/nested_hash_2_3'), 'value 2_3');
        $this->assertCount(3, ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_0'));
        $this->assertCount(2, ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1'));
        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_3'));
        ArrayUtils::setNestedArrayValue($copyArray, 'nested_hash/nested_hash_0/nested_hash_0_1', 'new value 0_1');
        ArrayUtils::setNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_2', 'new value 1_2');
        ArrayUtils::setNestedArrayValue($copyArray, 'nested_hash/nested_hash_3/nested_hash_3_0', 'new value 3_0');
        $this->assertNotSame('value 0_1', ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_0/nested_hash_0_1'));
        $this->assertNotNull(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_2'));
        $this->assertNotNull(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_3/nested_hash_3_0'));
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_0/nested_hash_0_1'), 'new value 0_1');
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_1'), 'value 1_1');
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1/nested_hash_1_2'), 'new value 1_2');
        $this->assertSame(ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_3/nested_hash_3_0'), 'new value 3_0');
        $this->assertCount(3, ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_0'));
        $this->assertCount(3, ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_1'));
        $this->assertCount(1, ArrayUtils::getNestedArrayValue($copyArray, 'nested_hash/nested_hash_3'));
    }

    public function testSetValueThrowsExceptionOnNonExistentKeyFilter(): void
    {
        $copyArray = $this->getCopyTestArray();

        $this->expectException(LogicException::class);
        ArrayUtils::setNestedArrayValue($copyArray, 'prices/@[code=ES]/price', '19.99 EUR');
        $this->assertNotSame(ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=ES]/price'), '19.99 EUR');
        $this->assertSame('9.99 EUR', ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=ES]/price'));

        $this->assertNull(ArrayUtils::getNestedArrayValue($copyArray, 'prices/@[code=IT]/price'));
        $this->expectException(LogicException::class);
        ArrayUtils::setNestedArrayValue($copyArray, 'prices/@[code=ES]/price', '29.99 EUR');
    }

    public function testMergeArraySuccess(): void
    {
        $left     = [
            'number'               => 1,
            'true'                 => true,
            'null'                 => null,
            'empty'                => '',
            'string'               => 'string [left]',
            'incoming_null'        => 'I have value [left]',
            'null_incoming_null'   => null,
            'incoming_empty'       => 'I have no empty string [left]',
            'empty_incoming_empty' => '',
        ];
        $right    = [
            'number'               => 20,
            'true'                 => false,
            'null'                 => 'overwrite null values [right]',
            'new_null'             => null,
            'new_empty'            => '',
            'new_string'           => 'I am a new string coming from right',
            'empty'                => 'overwrite empty values [right]',
            'string'               => 'string [right]',
            'incoming_null'        => null,
            'null_incoming_null'   => null,
            'incoming_empty'       => '',
            'empty_incoming_empty' => '',
        ];
        $expected = [
            'number'               => 20,
            'true'                 => false,
            'null'                 => 'overwrite null values [right]',
            'new_null'             => null,
            'new_empty'            => '',
            'new_string'           => 'I am a new string coming from right',
            'empty'                => 'overwrite empty values [right]',
            'string'               => 'string [right]',
            'incoming_null'        => 'I have value [left]',
            'null_incoming_null'   => null,
            'incoming_empty'       => 'I have no empty string [left]',
            'empty_incoming_empty' => '',
        ];
        $arrays   = [$left, $right];

        $merged = ArrayUtils::mergeNonEmpty(...$arrays);
        ksort($expected);
        ksort($merged);
        $this->assertSame($expected, $merged);
        $this->assertNotSame($merged, $left);
        $this->assertNotSame($merged, $right);
    }

}