<?php

namespace MediadataTv\Utils;

use LogicException;
use function trim;
use function ksort;
use function var_dump;
use function preg_match;
use function array_filter;
use function array_key_exists;
use function explode;
use function is_array;
use function strpos;
use function strtolower;
use function substr;

class ArrayUtils
{
    public const SUBARRAY_SELECTOR_FORMAT = '/^@\[(?P<key>.*)=(?P<value>.*)\]$/iu';

    /**
     * Filter a path of an associative array that meets all filter conditions.
     * If filter return more than 1 result it returns only one.
     * Returns null if no result from filter
     * By default string comparison is case sensitive.
     * Filter conditions can be inverted by prefixing filter key with an exclamation mark
     *
     * @param array  $array
     * @param string $path
     * @param array  $filter
     * @param string $pathDelimiter
     * @param bool   $caseInsensitive
     *
     * @return mixed | null
     */
    public static function getOneNestedFiltered(array $array, string $path, array $filter, string $pathDelimiter = '/', bool $caseInsensitive = false)
    {
        $filtered = self::getNestedFiltered($array, $path, $filter, $pathDelimiter, $caseInsensitive);
        if (count($filtered) > 0) {
            return array_values($filtered)[0];
        }

        return null;
    }

    /**
     * Filter a path of an associative array that meets all filter conditions
     * By default string comparison is case sensitive.
     * Filter conditions can be inverted by prefixing filter key with an exclamation mark
     *
     * @param array  $array
     * @param string $path
     * @param array  $filter
     * @param string $pathDelimiter
     * @param bool   $caseInsensitive
     *
     * @return array
     */
    public static function getNestedFiltered(array $array, string $path, array $filter, string $pathDelimiter = '/', bool $caseInsensitive = false): array
    {
        $toFilter = self::getNestedArrayValue($array, $path, $pathDelimiter);

        return self::filter($toFilter, $filter, $caseInsensitive);
    }

    /**
     * Searches in subarray using $delimiter parameter as splitter
     * Example:
     * <pre>
     * $array = [
     *  'first' => [
     *      'second' => [
     *          'third' => true
     *      ]
     *  ]
     * ];
     *
     * $value = ArrayUtils::getNestedArrayValue($array, 'first/second/third', '/', null);
     * </pre>
     *
     * Special filter can be applied with format <pre>@[key=value]</pre>
     * When defined special filter is specified only elements of array whose keys value is equal to value
     * specified in the expression are selected.
     *
     * <pre>
     * $array = [
     *  'countries' => [
     *      [
     *          'code' => 'ES',
     *          'active' => true,
     *          'price' => '9.99 EUR',
     *      ],
     *      [
     *          'code' => 'GB',
     *          'active' => false,
     *          'price' => '9.99 GBP',
     *      ],
     *      [
     *          'code' => 'US',
     *          'active' => false,
     *          'price' => '9.99 USD',
     *      ]
     *  ]
     * ];
     *
     * $value = ArrayUtils::getNestedArrayValue($array, 'countries/@[code=ES]/price', '/', null);
     * <pre>
     *
     * @param        $array
     * @param        $path
     * @param string $delimiter
     * @param mixed  $defaultValue
     *
     * @return bool
     */
    public static function getNestedArrayValue($array, $path, string $delimiter = '/', $defaultValue = null)
    {
        if ($path === null) {
            return $defaultValue;
        }
        $pathParts = explode($delimiter, $path);

        $current = &$array;
        foreach ($pathParts as $key) {
            $filterFound = false;
            if (is_array($current) && preg_match(self::SUBARRAY_SELECTOR_FORMAT, $key, $matches)) {
                foreach ($current as $ce) {
                    if (trim($ce[$matches['key']] ?? '') === trim($matches['value'])) {
                        $current     = &$ce;
                        $filterFound = true;
                        break;
                    }
                }
            }
            if ($filterFound) {
                continue;
            }
            if (is_array($current) && array_key_exists($key, $current)) {
                $current = &$current[$key];
            } else {
                return $defaultValue;
            }
        }

        return $current;
    }

    /**
     * Filter an associative array that meets all filter conditions
     * By default string comparison is case sensitive.
     * Filter conditions can be inverted by prefixing filter key with an exclamation mark
     *
     * @param array $array
     * @param array $filter
     * @param bool  $caseInsensitive
     *
     * @return array
     */
    public static function filter(array $array, array $filter, bool $caseInsensitive = false): array
    {
        return array_filter($array, static function ($item) use ($filter, $caseInsensitive) {
            $out = true;

            foreach ($filter as $k => $v) {
                $compareInverse = strpos($k, '!') === 0;
                if ($compareInverse) {
                    $k = substr($k, 1);
                }
                $origVal  = $item[$k] ?? null;
                $finalVal = $v;
                if ($caseInsensitive) {
                    $origVal  = strtolower($origVal);
                    $finalVal = strtolower($finalVal);
                }
                if ($compareInverse) {
                    $out = $out && ($origVal !== $finalVal);
                } else {
                    $out = $out && ($origVal === $finalVal);
                }
            }

            return $out;
        });
    }

    /**
     * Searches in subarray using $delimiter parameter as splitter and set value (or creates the key)
     *
     * @param        &$array
     * @param        $path
     * @param mixed  $value
     * @param string $delimiter
     *
     * @return bool
     * @throws LogicException
     * @see getNestedArrayValue for path search format
     */
    public static function setNestedArrayValue(&$array, $path, $value, string $delimiter = '/'): bool
    {
        if ($path === null) {
            return false;
        }
        $pathParts = explode($delimiter, $path);

        $current = &$array;

        foreach ($pathParts as $index => $key) {
            if (preg_match(self::SUBARRAY_SELECTOR_FORMAT, $key)) {
                throw new LogicException('Cannot filter with path and subarray selector, nor create nested path with this config.');
            }

            if (!array_key_exists($key, $current)) {
                $current[$key] = [];
            }

            if (is_array($current)) {
                $current = &$current[$key];
            }

        }

        $current = $value;

        return true;
    }

    /**
     * @param $array
     * @param $curr_key
     *
     * @return int|string|null
     */
    public static function getNext(&$array, $curr_key)
    {
        $next = 0;
        reset($array);

        do {
            $tmp_key = key($array);
            $res     = next($array);
        } while (($tmp_key !== $curr_key) && $res);

        if ($res) {
            $next = key($array);
        }

        return $array[$next];
    }

    /**
     * @param $array
     * @param $curr_key
     *
     * @return int|string|null
     */
    public static function getPrev(&$array, $curr_key)
    {
        end($array);
        $prev = key($array);

        do {
            $tmp_key = key($array);
            $res     = prev($array);
        } while (($tmp_key !== $curr_key) && $res);

        if ($res) {
            $prev = key($array);
        }

        return $array[$prev];
    }

    /**
     * Sort multidimensional nested array by value recursively
     * Maintains keys if associative.
     *
     * @param $arr
     */
    public static function sortArrayRecursive(&$arr): void
    {
        if (self::isAssoc($arr)) {
            ksort($arr);
        } else {
            sort($arr);
        }
        foreach ($arr as &$a) {
            if (is_array($a)) {
                self::sortArrayRecursive($a);
            }
        }
    }

    /**
     * Checks if the array parameter is associative or not
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        // Keys of the array
        $keys = array_keys($array);

        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }

}
