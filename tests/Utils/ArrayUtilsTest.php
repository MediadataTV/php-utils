<?php

use PHPUnit\Framework\TestCase;
use MediadataTv\Utils\ArrayUtils;
use MediadataTv\Utils\StringUtils;

class ArrayUtilsTest extends TestCase
{


    private array $testArray;
    private array $testArrayUnset;

    protected function setUp(): void
    {
        $this->testArray = [
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

        $this->testArrayUnset = [
            'user'     => [
                'profile' => [
                    'name'      => 'John',
                    'age'       => 30,
                    'external'  => [
                        'data' => [
                            'identifier' => 'A123456',
                            'name'       => 'Service 2',
                        ],
                    ],
                    'contacts'  => [
                        ['phone' => '1234567890', 'type' => 'mobile'],
                        ['phone' => '0987654321', 'type' => 'work'],
                    ],
                    'addresses' => [
                        [
                            'street' => '123 Main St', 'city' => 'New York',
                            'geo'    => ['lat' => 40.7128, 'lng' => -74.0060, 'accuracy' => 100],
                        ],
                        [
                            'street' => '456 Elm St', 'city' => 'Boston',
                            'geo'    => ['lat' => 42.3601, 'lng' => -71.0589, 'accuracy' => 1],
                        ],
                    ],
                    'settings'  => [
                        'theme'         => 'dark',
                        'notifications' => true,
                    ],
                ],
            ],
            'products' => [
                'inventory' => [
                    [
                        'name'  => 'Apple', 'quantity' => 10,
                        'store' => [
                            [
                                'country' => 'Spain', 'city' => 'Barcelona', 'year' => 2025,
                                'meta'    => ['created_at' => '2025-01-01', 'updated_at' => '2025-06-01'],
                            ],
                            [
                                'country' => 'Spain', 'city' => 'Madrid', 'year' => 2024,
                                'meta'    => ['created_at' => '2024-01-01', 'updated_at' => '2024-06-01'],
                            ],
                        ],
                    ],
                    [
                        'name'  => 'Banana', 'quantity' => 20,
                        'store' => [
                            [
                                'country' => 'Spain', 'city' => 'Barcelona', 'year' => 2025,
                                'meta'    => ['created_at' => '2025-01-01', 'updated_at' => '2025-06-01'],
                            ],
                            [
                                'country' => 'Spain', 'city' => 'Madrid', 'year' => 2024,
                                'meta'    => ['created_at' => '2024-01-01', 'updated_at' => '2024-06-01'],
                            ],
                        ],
                    ],
                ],
            ],
            'empty'    => [],
            'scalar'   => 'value',
            'numeric'  => [1, 2, 3],
            0          => 'zero-index',
            'null'     => null,
        ];
    }

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

    public function testBasicUnset(): void
    {
        $paths  = ['user/profile/age'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('profile', $result['user']);
        $this->assertArrayNotHasKey('age', $result['user']['profile']);
        $this->assertArrayHasKey('name', $result['user']['profile']);
    }

    public function testMultiplePathsUnset(): void
    {
        $paths = [
            'user/profile/settings/theme',
            'scalar',
        ];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayNotHasKey('theme', $result['user']['profile']['settings']);
        $this->assertArrayNotHasKey('scalar', $result);
    }

    public function testCustomDelimiter(): void
    {
        $paths  = ['user.profile.settings.theme'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths, '.');

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayNotHasKey('theme', $result['user']['profile']['settings']);
    }

    public function testDeepNestedPath(): void
    {
        $paths  = ['user/profile/external/data/identifier'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertArrayHasKey('data', $result['user']['profile']['external']);
        $this->assertArrayNotHasKey('identifier', $result['user']['profile']['external']['data']);
        $this->assertArrayHasKey('name', $result['user']['profile']['external']['data']);
    }

    public function testNonExistentPaths(): void
    {
        $paths = [
            'user/profile/nonexistent',
            'nonexistent/path',
            'user/profile/contacts/nonexistent/deeper',
        ];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Array should remain unchanged
        $this->assertEquals($this->testArrayUnset, $result);
    }

    public function testEmptyPaths(): void
    {
        $paths  = [];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertEquals($this->testArrayUnset, $result);
    }

    public function testNumericKeys(): void
    {
        $paths = [
            'numeric/0',
            '0',  // Root level numeric key
        ];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertArrayHasKey('numeric', $result);
        $this->assertNotContains(1, $result['numeric']);
        $this->assertArrayNotHasKey(0, $result);
    }

    public function testPathThroughNonArrays(): void
    {
        $paths = [
            'scalar/something',              // scalar is not an array
            'null/something',                // null is not an array
            'user/profile/name/impossible',  // name is not an array
        ];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Array should remain unchanged
        $this->assertEquals($this->testArrayUnset, $result);
    }

    public function testEmptyStringPath(): void
    {
        $paths  = [''];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Array should remain unchanged
        $this->assertEquals($this->testArrayUnset, $result);
    }

    public function testPathWithEmptySegments(): void
    {
        $paths  = ['user//profile///age'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Should not unset anything due to invalid path
        $this->assertEquals($this->testArrayUnset, $result);
    }

    public function testOriginalArrayUnchanged(): void
    {
        $original = $this->testArrayUnset;
        $paths    = ['user/profile/age'];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Original array should remain unchanged
        $this->assertEquals($original, $this->testArrayUnset);
        // Result should be different
        $this->assertNotEquals($original, $result);
    }

    public function testUnsetNullValueUnchanged(): void
    {
        $original = $this->testArrayUnset;
        $paths    = ['null'];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        // Original array should remain unchanged
        $this->assertEquals($original, $this->testArrayUnset);
        // Result should be different
        $this->assertNotEquals($original, $result);
    }

    public function testWildcardArrayUnset(): void
    {
        $paths  = ['user/profile/addresses/[]/street'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertCount(2, $result['user']['profile']['addresses']);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][0]);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][1]);
        $this->assertSame('New York', $result['user']['profile']['addresses'][0]['city']);
        $this->assertSame('Boston', $result['user']['profile']['addresses'][1]['city']);
    }

    public function testMultipleWildcardUnset(): void
    {
        $paths  = [
            'user/profile/addresses/[]/street',
            'user/profile/contacts/[]/type',
        ];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertCount(2, $result['user']['profile']['addresses']);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][0]);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][1]);

        $this->assertCount(2, $result['user']['profile']['contacts']);
        $this->assertArrayNotHasKey('type', $result['user']['profile']['contacts'][0]);
        $this->assertArrayNotHasKey('type', $result['user']['profile']['contacts'][1]);
    }

    public function testWildcardWithLoopHash(): void
    {
        $paths  = ['user/profile/addresses/[]/city'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertCount(2, $result['user']['profile']['addresses']);
        $this->assertArrayNotHasKey('city', $result['user']['profile']['addresses'][0]);
        $this->assertArrayNotHasKey('city', $result['user']['profile']['addresses'][1]);
        $this->assertSame('123 Main St', $result['user']['profile']['addresses'][0]['street']);
        $this->assertSame('456 Elm St', $result['user']['profile']['addresses'][1]['street']);
    }

    public function testWildcardWithLoopHashDeepNested(): void
    {
        $paths  = ['user/profile/addresses/[]/geo/accuracy'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertCount(2, $result['user']['profile']['addresses']);
        $this->assertArrayHasKey('geo', $result['user']['profile']['addresses'][0]);
        $this->assertArrayHasKey('geo', $result['user']['profile']['addresses'][1]);
        $this->assertArrayHasKey('lat', $result['user']['profile']['addresses'][0]['geo']);
        $this->assertArrayHasKey('lat', $result['user']['profile']['addresses'][1]['geo']);
        $this->assertArrayHasKey('lng', $result['user']['profile']['addresses'][0]['geo']);
        $this->assertArrayHasKey('lng', $result['user']['profile']['addresses'][1]['geo']);
        $this->assertArrayNotHasKey('accuracy', $result['user']['profile']['addresses'][0]['geo']);
        $this->assertArrayNotHasKey('accuracy', $result['user']['profile']['addresses'][1]['geo']);
        $this->assertSame('123 Main St', $result['user']['profile']['addresses'][0]['street']);
        $this->assertSame('456 Elm St', $result['user']['profile']['addresses'][1]['street']);
    }

    public function testDoubleWildcardDeepNestedRemoval(): void
    {
        $paths = [
            'products/inventory/[]/store/[]/year',
            'products/inventory/[]/store/[]/meta/created_at',
        ];

        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        foreach ($result['products']['inventory'] as $product) {
            foreach ($product['store'] as $store) {
                $this->assertArrayHasKey('country', $store);
                $this->assertArrayHasKey('city', $store);
                $this->assertArrayHasKey('meta', $store);
                $this->assertArrayHasKey('updated_at', $store['meta']);

                $this->assertArrayNotHasKey('year', $store);
                $this->assertArrayNotHasKey('created_at', $store['meta']);
            }
        }
    }

    public function testWildcardMixedWithNormalPath(): void
    {
        $paths  = [
            'user/profile/addresses/[]/street',
            'products/inventory/[]/name',
        ];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertCount(2, $result['user']['profile']['addresses']);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][0]);
        $this->assertArrayNotHasKey('street', $result['user']['profile']['addresses'][1]);

        $this->assertCount(2, $result['products']['inventory']);
        $this->assertArrayNotHasKey('name', $result['products']['inventory'][0]);
        $this->assertArrayNotHasKey('name', $result['products']['inventory'][1]);
    }

    public function testWildcardWithNonExistentPath(): void
    {
        $paths  = ['user/nonexistent/[]/street'];
        $result = ArrayUtils::unsetNestedArray($this->testArrayUnset, $paths);

        $this->assertEquals($this->testArrayUnset, $result);
    }

}