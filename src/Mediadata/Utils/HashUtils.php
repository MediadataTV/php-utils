<?php

namespace Mediadata\Utils;

use InvalidArgumentException;
use function sprintf;
use function in_array;
use function hash_algos;
use function json_encode;

class HashUtils
{
    public const HASH_MD5    = 'md5';
    public const HASH_SHA1   = 'sha1';
    public const HASH_SHA256 = 'sha256';
    public const HASH_SHA512 = 'sha512';
    public const HASH_CRC32  = 'crc32';

    public const VALID_DIRECT_ALGOS = [
        self::HASH_MD5,
        self::HASH_SHA1,
        self::HASH_CRC32,
    ];

    /**
     * @param array $data
     * @param bool  $skipHashWrapper
     *
     * @return false|string
     */
    public static function md5Array(array $data, bool $skipHashWrapper = false)
    {
        return self::hashArray($data, self::HASH_MD5, $skipHashWrapper);
    }

    /**
     * @param array  $data
     * @param string $type
     * @param bool   $skipHashWrapper
     *
     * @return false|string
     */
    public static function hashArray(array $data, string $type, bool $skipHashWrapper = false)
    {
        if ($skipHashWrapper) {
            $validAlgorithms = self::VALID_DIRECT_ALGOS;
        } else {
            $validAlgorithms = hash_algos();
        }
        if (!in_array($type, $validAlgorithms, true)) {
            throw new InvalidArgumentException(sprintf('Hash alghorithm `%s` not valid (@see hash_algos function)', $type));
        }

        ArrayUtils::sortArrayRecursive($data);
        $strData = json_encode($data);
        if ($skipHashWrapper) {
            return $type($strData);
        }

        return hash($type, $strData);
    }

    /**
     * @param array $data
     * @param bool  $skipHashWrapper
     *
     * @return false|string
     */
    public static function sha1Array(array $data, bool $skipHashWrapper = false)
    {
        return self::hashArray($data, self::HASH_SHA1, $skipHashWrapper);
    }

    /**
     * @param array $data
     * @param bool  $skipHashWrapper
     *
     * @return false|string
     */
    public static function sha256Array(array $data, bool $skipHashWrapper = false)
    {
        return self::hashArray($data, self::HASH_SHA256, $skipHashWrapper);
    }

    /**
     * @param array $data
     * @param bool  $skipHashWrapper
     *
     * @return false|string
     */
    public static function sha512Array(array $data, bool $skipHashWrapper = false)
    {
        return self::hashArray($data, self::HASH_SHA512, $skipHashWrapper);
    }

}
