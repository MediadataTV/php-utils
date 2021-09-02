<?php

namespace MediadataTv\Utils;

use function trim;
use function sprintf;

class RegexUtils
{
    /**
     * Converts a generic array of regex config in array of current regexes
     * where key is the pattern and value is the replacement
     *
     * @param        $config
     *
     * @return array
     */
    public static function configToRegexReplaceArray($config): array
    {
        $out = [];
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

        return $out;
    }

    /**
     * Converts a generic array of regex config in array of current regexes
     * where key is the pattern and value is the replacement
     *
     * @deprecated Use MediadataTv\Utils\RegexUtils::configToRegexReplaceArray instead will be removed in v1.1.0
     *
     * @param        $config
     *
     * @return array
     */
    public static function configToRegexArray($config): array
    {

    }

    /**
     * Converts a generic array of regex match config in array of current regexes
     *
     * @param        $config
     *
     * @return array
     */
    public static function configToRegexMatchArray($config): array
    {
        $out = [];
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
        }

        return $out;
    }


}
