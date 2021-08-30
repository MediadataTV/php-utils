<?php

namespace Mediadata\Utils;

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
    public static function configToRegexArray($config)
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


}
