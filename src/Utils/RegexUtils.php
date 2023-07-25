<?php

namespace MediadataTv\Utils;

use function trim;
use function sprintf;
use function is_array;
use function preg_quote;

class RegexUtils
{
    /**
     * Converts a generic array of regex config in array of current regexes
     * where key is the pattern and value is the replacement
     *
     * @param null|array $config
     * @param bool       $regQuote
     * @return array
     */
    public static function configToRegexReplaceArray(?array $config, bool $regQuote = false): array
    {
        $out = [];
        if (is_array($config)) {
            foreach ($config as $c) {
                $key = $c['from'];
                if ($key === null || trim($key) === '') {
                    continue;
                }
                if ($regQuote === true) {
                    $key = preg_quote($key, '/');
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
     * Converts a generic array of regex match config in array of current regexes
     *
     * @param array|null $config
     * @param bool       $regQuote
     * @return array
     */
    public static function configToRegexMatchArray(?array $config, bool $regQuote = false): array
    {
        $out = [];
        if (is_array($config)) {
            foreach ($config as $c) {
                $match = $c['from'] ?? null;
                if ($match === null || trim($match) === '') {
                    continue;
                }
                if ($regQuote === true) {
                    $match = preg_quote($match, '/');
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
