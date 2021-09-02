<?php

namespace MediadataTv\Utils;

use function trim;
use function sprintf;
use function is_array;

class RegexUtils
{
    /**
     * Converts a generic array of regex config in array of current regexes
     * where key is the pattern and value is the replacement
     *
     * @param null|array $config
     *
     * @return array
     */
    public static function configToRegexReplaceArray(?array $config): array
    {
        $out = [];
        if(is_array($config)) {
            foreach ($config as $c) {
                $key = $c['from'];
                if ($key === null || trim($key) === '') {
                    continue;
                }
                switch ($c['type']) {
                    case 'contains':
                        $key = sprintf('/%s/iu', $key);
                        break;
                    case 'startsWith':
                        $key = sprintf('/^%s/iu', $key);
                        break;
                    case 'endsWith':
                        $key = sprintf('/%s$/iu', $key);
                        break;
                    case 'exact':
                    default:
                        $key = sprintf('/^%s$/iu', $key);
                        break;
                }
                $out[$key] = $c['to'];
            }
        }
        return $out;
    }

    /**
     * Converts a generic array of regex config in array of current regexes
     * where key is the pattern and value is the replacement
     *
     * @param array|null $config
     *
     * @return array
     * @deprecated Will be removed in v1.1.0 . Use MediadataTv\Utils\RegexUtils::configToRegexReplaceArray instead.
     *
     */
    public static function configToRegexArray(?array $config): array
    {
        return self::configToRegexReplaceArray($config);
    }

    /**
     * Converts a generic array of regex match config in array of current regexes
     *
     * @param array|null $config
     *
     * @return array
     */
    public static function configToRegexMatchArray(?array $config): array
    {
        $out = [];
        if(is_array($config)) {
            foreach ($config as $c) {
                $match = $c['from'] ?? null;
                if ($match === null || trim($match) === '') {
                    continue;
                }
                switch ($c['type']) {
                    case 'contains':
                        $out[] = sprintf('/%s/iu', $match);
                        break;
                    case 'startsWith':
                        $out[] = sprintf('/^%s/iu', $match);
                        break;
                    case 'endsWith':
                        $out[] = sprintf('/%s$/iu', $match);
                        break;
                    case 'exact':
                    default:
                        $out[] = sprintf('/^%s$/iu', $match);
                        break;
                }
            }
        }

        return $out;
    }

}
