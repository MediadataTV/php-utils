<?php

namespace MediadataTv\Utils;

use Normalizer;
use function substr;
use function sprintf;
use function array_keys;
use function array_merge;
use function array_values;
use function html_entity_decode;
use function htmlentities;
use function levenshtein;
use function mb_strtolower;
use function mb_strtoupper;
use function preg_replace;
use function preg_replace_callback;
use function str_replace;
use function strcasecmp;
use function strcmp;
use function trim;
use function ucfirst;
use const ENT_QUOTES;


define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));

class StringUtils
{

    /**
     * @param $string
     *
     * @return false|string
     */
    public static function detectEncoding($string)
    {
        $first2 = substr($string, 0, 2);
        $first3 = substr($string, 0, 3);
        $first4 = substr($string, 0, 3);

        if ($first3 === UTF8_BOM) {
            return 'UTF-8';
        }

        if ($first4 === UTF32_BIG_ENDIAN_BOM) {
            return 'UTF-32BE';
        }

        if ($first4 === UTF32_LITTLE_ENDIAN_BOM) {
            return 'UTF-32LE';
        }

        if ($first2 === UTF16_BIG_ENDIAN_BOM) {
            return 'UTF-16BE';
        }

        if ($first2 === UTF16_LITTLE_ENDIAN_BOM) {
            return 'UTF-16LE';
        }

        return mb_detect_encoding($string, ' UTF-8, ISO-8859-1, ASCII', true);
    }

    /**
     * @param $string
     *
     * @return string|string[]|null
     */
    public static function removeEmoji($string)
    {
        return preg_replace('/[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0077}\x{E006C}\x{E0073}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0073}\x{E0063}\x{E0074}\x{E007F})|[\x{1F3F4}](?:\x{E0067}\x{E0062}\x{E0065}\x{E006E}\x{E0067}\x{E007F})|[\x{1F3F4}](?:\x{200D}\x{2620}\x{FE0F})|[\x{1F3F3}](?:\x{FE0F}\x{200D}\x{1F308})|[\x{0023}\x{002A}\x{0030}\x{0031}\x{0032}\x{0033}\x{0034}\x{0035}\x{0036}\x{0037}\x{0038}\x{0039}](?:\x{FE0F}\x{20E3})|[\x{1F415}](?:\x{200D}\x{1F9BA})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F467})|[\x{1F468}](?:\x{200D}\x{1F468}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467}\x{200D}\x{1F466})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F467})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F469}\x{200D}\x{1F466})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F468})|[\x{1F469}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F469})|[\x{1F469}\x{1F468}](?:\x{200D}\x{2764}\x{FE0F}\x{200D}\x{1F48B}\x{200D}\x{1F468})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9BD})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9AF})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2640}\x{FE0F})|[\x{1F575}\x{1F3CC}\x{26F9}\x{1F3CB}](?:\x{FE0F}\x{200D}\x{2642}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F692})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F680})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2708}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A8})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3A4})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F52C})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F4BC})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3ED})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F527})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F373})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F33E})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2696}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F3EB})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F393})|[\x{1F468}\x{1F469}](?:\x{200D}\x{2695}\x{FE0F})|[\x{1F471}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F9CF}\x{1F647}\x{1F926}\x{1F937}\x{1F46E}\x{1F482}\x{1F477}\x{1F473}\x{1F9B8}\x{1F9B9}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F486}\x{1F487}\x{1F6B6}\x{1F9CD}\x{1F9CE}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}\x{1F9D8}](?:\x{200D}\x{2640}\x{FE0F})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B2})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B3})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B1})|[\x{1F468}\x{1F469}](?:\x{200D}\x{1F9B0})|[\x{1F471}\x{1F64D}\x{1F64E}\x{1F645}\x{1F646}\x{1F481}\x{1F64B}\x{1F9CF}\x{1F647}\x{1F926}\x{1F937}\x{1F46E}\x{1F482}\x{1F477}\x{1F473}\x{1F9B8}\x{1F9B9}\x{1F9D9}\x{1F9DA}\x{1F9DB}\x{1F9DC}\x{1F9DD}\x{1F9DE}\x{1F9DF}\x{1F486}\x{1F487}\x{1F6B6}\x{1F9CD}\x{1F9CE}\x{1F3C3}\x{1F46F}\x{1F9D6}\x{1F9D7}\x{1F3C4}\x{1F6A3}\x{1F3CA}\x{1F6B4}\x{1F6B5}\x{1F938}\x{1F93C}\x{1F93D}\x{1F93E}\x{1F939}\x{1F9D8}](?:\x{200D}\x{2642}\x{FE0F})|[\x{1F441}](?:\x{FE0F}\x{200D}\x{1F5E8}\x{FE0F})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FA}](?:\x{1F1FF})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1FA}](?:\x{1F1FE})|[\x{1F1E6}\x{1F1E8}\x{1F1F2}\x{1F1F8}](?:\x{1F1FD})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F9}\x{1F1FF}](?:\x{1F1FC})|[\x{1F1E7}\x{1F1E8}\x{1F1F1}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1FB})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1FB}](?:\x{1F1FA})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FE}](?:\x{1F1F9})|[\x{1F1E6}\x{1F1E7}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FA}\x{1F1FC}](?:\x{1F1F8})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F7})|[\x{1F1E6}\x{1F1E7}\x{1F1EC}\x{1F1EE}\x{1F1F2}](?:\x{1F1F6})|[\x{1F1E8}\x{1F1EC}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}](?:\x{1F1F5})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EE}\x{1F1EF}\x{1F1F2}\x{1F1F3}\x{1F1F7}\x{1F1F8}\x{1F1F9}](?:\x{1F1F4})|[\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1F3})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1EC}\x{1F1ED}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F4}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FF}](?:\x{1F1F2})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1F1})|[\x{1F1E8}\x{1F1E9}\x{1F1EB}\x{1F1ED}\x{1F1F1}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FD}](?:\x{1F1F0})|[\x{1F1E7}\x{1F1E9}\x{1F1EB}\x{1F1F8}\x{1F1F9}](?:\x{1F1EF})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EB}\x{1F1EC}\x{1F1F0}\x{1F1F1}\x{1F1F3}\x{1F1F8}\x{1F1FB}](?:\x{1F1EE})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F5}\x{1F1F8}\x{1F1F9}](?:\x{1F1ED})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}](?:\x{1F1EC})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F9}\x{1F1FC}](?:\x{1F1EB})|[\x{1F1E6}\x{1F1E7}\x{1F1E9}\x{1F1EA}\x{1F1EC}\x{1F1EE}\x{1F1EF}\x{1F1F0}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F7}\x{1F1F8}\x{1F1FB}\x{1F1FE}](?:\x{1F1EA})|[\x{1F1E6}\x{1F1E7}\x{1F1E8}\x{1F1EC}\x{1F1EE}\x{1F1F2}\x{1F1F8}\x{1F1F9}](?:\x{1F1E9})|[\x{1F1E6}\x{1F1E8}\x{1F1EA}\x{1F1EE}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F8}\x{1F1F9}\x{1F1FB}](?:\x{1F1E8})|[\x{1F1E7}\x{1F1EC}\x{1F1F1}\x{1F1F8}](?:\x{1F1E7})|[\x{1F1E7}\x{1F1E8}\x{1F1EA}\x{1F1EC}\x{1F1F1}\x{1F1F2}\x{1F1F3}\x{1F1F5}\x{1F1F6}\x{1F1F8}\x{1F1F9}\x{1F1FA}\x{1F1FB}\x{1F1FF}](?:\x{1F1E6})|[\x{00A9}\x{00AE}\x{203C}\x{2049}\x{2122}\x{2139}\x{2194}-\x{2199}\x{21A9}-\x{21AA}\x{231A}-\x{231B}\x{2328}\x{23CF}\x{23E9}-\x{23F3}\x{23F8}-\x{23FA}\x{24C2}\x{25AA}-\x{25AB}\x{25B6}\x{25C0}\x{25FB}-\x{25FE}\x{2600}-\x{2604}\x{260E}\x{2611}\x{2614}-\x{2615}\x{2618}\x{261D}\x{2620}\x{2622}-\x{2623}\x{2626}\x{262A}\x{262E}-\x{262F}\x{2638}-\x{263A}\x{2640}\x{2642}\x{2648}-\x{2653}\x{265F}-\x{2660}\x{2663}\x{2665}-\x{2666}\x{2668}\x{267B}\x{267E}-\x{267F}\x{2692}-\x{2697}\x{2699}\x{269B}-\x{269C}\x{26A0}-\x{26A1}\x{26AA}-\x{26AB}\x{26B0}-\x{26B1}\x{26BD}-\x{26BE}\x{26C4}-\x{26C5}\x{26C8}\x{26CE}-\x{26CF}\x{26D1}\x{26D3}-\x{26D4}\x{26E9}-\x{26EA}\x{26F0}-\x{26F5}\x{26F7}-\x{26FA}\x{26FD}\x{2702}\x{2705}\x{2708}-\x{270D}\x{270F}\x{2712}\x{2714}\x{2716}\x{271D}\x{2721}\x{2728}\x{2733}-\x{2734}\x{2744}\x{2747}\x{274C}\x{274E}\x{2753}-\x{2755}\x{2757}\x{2763}-\x{2764}\x{2795}-\x{2797}\x{27A1}\x{27B0}\x{27BF}\x{2934}-\x{2935}\x{2B05}-\x{2B07}\x{2B1B}-\x{2B1C}\x{2B50}\x{2B55}\x{3030}\x{303D}\x{3297}\x{3299}\x{1F004}\x{1F0CF}\x{1F170}-\x{1F171}\x{1F17E}-\x{1F17F}\x{1F18E}\x{1F191}-\x{1F19A}\x{1F201}-\x{1F202}\x{1F21A}\x{1F22F}\x{1F232}-\x{1F23A}\x{1F250}-\x{1F251}\x{1F300}-\x{1F321}\x{1F324}-\x{1F393}\x{1F396}-\x{1F397}\x{1F399}-\x{1F39B}\x{1F39E}-\x{1F3F0}\x{1F3F3}-\x{1F3F5}\x{1F3F7}-\x{1F3FA}\x{1F400}-\x{1F4FD}\x{1F4FF}-\x{1F53D}\x{1F549}-\x{1F54E}\x{1F550}-\x{1F567}\x{1F56F}-\x{1F570}\x{1F573}-\x{1F57A}\x{1F587}\x{1F58A}-\x{1F58D}\x{1F590}\x{1F595}-\x{1F596}\x{1F5A4}-\x{1F5A5}\x{1F5A8}\x{1F5B1}-\x{1F5B2}\x{1F5BC}\x{1F5C2}-\x{1F5C4}\x{1F5D1}-\x{1F5D3}\x{1F5DC}-\x{1F5DE}\x{1F5E1}\x{1F5E3}\x{1F5E8}\x{1F5EF}\x{1F5F3}\x{1F5FA}-\x{1F64F}\x{1F680}-\x{1F6C5}\x{1F6CB}-\x{1F6D2}\x{1F6D5}\x{1F6E0}-\x{1F6E5}\x{1F6E9}\x{1F6EB}-\x{1F6EC}\x{1F6F0}\x{1F6F3}-\x{1F6FA}\x{1F7E0}-\x{1F7EB}\x{1F90D}-\x{1F93A}\x{1F93C}-\x{1F945}\x{1F947}-\x{1F971}\x{1F973}-\x{1F976}\x{1F97A}-\x{1F9A2}\x{1F9A5}-\x{1F9AA}\x{1F9AE}-\x{1F9CA}\x{1F9CD}-\x{1F9FF}\x{1FA70}-\x{1FA73}\x{1FA78}-\x{1FA7A}\x{1FA80}-\x{1FA82}\x{1FA90}-\x{1FA95}]/u', '', $string);
    }

    /**
     * Normalizes combining characters to unicode standard C form
     *
     * @see https://en.wikipedia.org/wiki/Combining_character
     *
     * @param string $string Input string
     * @param string $form   Normalization form [By default FORM_C]
     *
     * @return string
     */
    public static function normalizeCombiningChars(string $string, $form = Normalizer::FORM_C)
    {
        return Normalizer::normalize($string, $form);
    }

    /**
     * @param $string
     *
     * @return false|string
     */
    public static function transliterateToAscii($string)
    {
        $string = strtr($string, UTF8ToAsciiMap::MAP);
        $string = preg_replace('/[‚‚]/u', ',', $string);
        $string = preg_replace('/[`‛′’‘]/u', "'", $string);
        $string = preg_replace('/[″“”«»„]/u', '"', $string);
        $string = preg_replace('/[—–―−–‾⌐─↔→←]/u', '-', $string);
        $string = preg_replace('/[  ]/u', ' ', $string);
        $string = str_replace('…', '...', $string);

        return $string;
    }

    /**
     * slugifies a string, using the selected separator
     *
     * @param string $string
     * @param string $separator
     * @param string $encoding
     *
     * @return string
     */
    public static function slugify(string $string, string $separator = '-', string $encoding = 'UTF-8'): string
    {
        if ($encoding === null) {
            $encoding = 'UTF-8';
        }
        $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
        $special_cases = ['&' => 'and', "'" => ''];
        $tmp           = mb_strtolower(trim($string), $encoding);
        $tmp           = html_entity_decode($tmp);
        $tmp           = str_replace(array_keys($special_cases), array_values($special_cases), $tmp);
        $tmp           = preg_replace($accents_regex, '$1', htmlentities($tmp, ENT_QUOTES, $encoding));
        $tmp           = str_replace(['&iquest;', 'andhellip'], '', $tmp);
        $tmp           = preg_replace('/[^a-z0-9]/u', (string)$separator, $tmp);
        $tmp           = preg_replace("/[$separator]+/u", (string)$separator, $tmp);

        return trim($tmp, $separator);
    }

    /**
     * Trims the removes BS (?!?!?!)
     *
     * @param $string
     *
     * @return string
     */
    public static function removeBS($string): string
    {
        $stringArray = str_split(trim($string));
        $newString   = '';
        foreach ($stringArray as $char) {
            $charNum = ord($char);
            if ($charNum === 163) {
                $newString .= $char;
                continue;
            } // keep £
            if ($charNum > 31 && $charNum < 127) {
                $newString .= $char;
            }
        }

        return $newString;
    }

    /**
     * @param $string
     * @return array|string|string[]
     */
    public static function replaceNonPrintable($string)
    {
        return str_replace(array_keys(UTF8ToAsciiMap::NON_PRINTABLE), array_values(UTF8ToAsciiMap::NON_PRINTABLE), $string);
    }

    /**
     * @param       $string
     * @param false $removePunctuation
     * @param array $additionalReplaces
     * @return array|false|string|string[]
     */
    public static function transliterateForAltMemoryKey($string, $removePunctuation = false, $additionalReplaces = [])
    {
        $string   = mb_strtoupper($string);
        $string   = self::replaceNonPrintable($string);
        $string   = self::transliterateToAscii($string);
        $replaces = array_merge(
            [
                ' ' => '',
                "'" => '',
                'º' => 'O',
                'ª' => 'A',
            ],
            $additionalReplaces
        );
        if ($removePunctuation === true) {
            $replaces = array_merge($replaces, [
                '.' => '',
                ',' => '',
                ';' => '',
                ':' => '',
                '´' => '',
                '`' => '',
            ]);
        }

        return str_replace(array_keys($replaces), array_values($replaces), $string);
    }

    /**
     * @param       $string
     * @param array $additionalReplaces
     * @return array|string|string[]
     */
    public static function normalizeStringForMemoryKey($string, $additionalReplaces = [])
    {
        $string   = mb_strtoupper(trim($string));
        $replaces = array_merge(
            [
                "'"        => '',
                ' '        => '',
                '&'        => 'AND',
                'Á'        => 'A',
                'É'        => 'E',
                'Í'        => 'I',
                'Ó'        => 'O',
                'Ú'        => 'U',
                'Ř'        => 'R',
                "\xc2\xA0" => '',
            ],
            $additionalReplaces
        );
        return str_replace(array_keys($replaces), array_values($replaces), $string);
    }

    /**
     * @param $from
     * @param $to
     *
     * @return bool
     */
    public static function asciiCompareExact($from, $to): bool
    {
        return self::asciiCompareLevenshtein($from, $to, 0);
    }

    /**
     * @param      $from
     * @param      $to
     * @param int  $distance
     * @param null $levenshtein
     *
     * @return bool
     */
    public static function asciiCompareLevenshteinSkipEmpty($from, $to, int $distance = 1, &$levenshtein = null): bool
    {
        if (trim($from) === '' && trim($to) === '') {
            return false;
        }

        return self::asciiCompareLevenshtein($from, $to, $distance, $levenshtein);
    }

    /**
     * @param      $from
     * @param      $to
     * @param int  $distance
     * @param null $levenshtein
     *
     * @return bool
     */
    public static function asciiCompareLevenshtein($from, $to, int $distance = 1, &$levenshtein = null): bool
    {
        $replaces = [
            '/\s+\/\s+/' => '/',
            '/\s+\\\s+/' => '\\',
            '/\s+/'      => ' ',
        ];

        $fromNorm = self::transliterateToAscii(mb_strtolower(trim($from)));
        $toNorm   = self::transliterateToAscii(mb_strtolower(trim($to)));
        $fromNorm = preg_replace(array_keys($replaces), array_values($replaces), $fromNorm);
        $toNorm   = preg_replace(array_keys($replaces), array_values($replaces), $toNorm);
        if ($distance >= 1) {
            $levenshtein = levenshtein($fromNorm, $toNorm);

            return ($levenshtein <= $distance);
        }

        return strcmp($fromNorm, $toNorm) === 0;
    }

    /**
     * Convert multibyte characters not present in latin1 map (char code > 255) to html numeric entities
     *
     * @param $string
     *
     * @return string
     */
    public static function toIsoHtmlEntities($string): string
    {
        return mb_encode_numericentity($string, [0x0100, 0xFFFF, 0, 0xFFFF], 'UTF-8');
    }

    /**
     * @param      $string
     * @param null $flags
     *
     * @return string
     */
    public static function htmlEntitiesDecode($string, $flags = null): string
    {
        $replaces = [
            '&apos;' => '&#39;',
        ];
        $string   = str_replace(array_keys($replaces), array_values($replaces), $string);

        return html_entity_decode($string, $flags);
    }

    /**
     * @param      $string1
     * @param      $string2
     * @param bool $caseInsensitive
     *
     * @return bool True if string are the same, false otherwise
     */
    public static function compare($string1, $string2, bool $caseInsensitive = true): bool
    {
        $a = trim($string1);
        $b = trim($string2);
        if ($caseInsensitive) {
            return strcasecmp($a, $b) === 0;
        }

        return strcmp($a, $b) === 0;
    }

    /**
     * @param $str
     *
     * @return string|string[]|null
     */
    public static function ucfirstSentence($str)
    {
        $str = ucfirst(mb_strtolower($str));
        return preg_replace_callback(
            '/([.!?¡¿:])\s*(\w)/u',
            static function ($matches) {
                return mb_strtoupper($matches[0]);
            },
            $str
        );
    }

    /**
     * @param string $text
     * @param bool   $outputHtmlEncoded
     *
     * @return string
     */
    public static function fixAmpersandLoop(string $text, bool $outputHtmlEncoded = false): string
    {
        return preg_replace('/&(amp;)+/m', $outputHtmlEncoded ? '&amp;' : '&', $text);
    }

    /**
     * @param string $text
     * @return string
     */
    public static function fixAmpersandLoopAndHtmlDecode(string $text): string
    {
        return self::htmlEntitiesDecode(self::fixAmpersandLoop($text), ENT_QUOTES);
    }

    /**
     * @param        $input
     * @param string $decimalSeparator
     *
     * @return float
     */
    public static function stringToFloat($input, string $decimalSeparator = ','): float
    {
        return (float)str_replace($decimalSeparator, '.', $input);
    }

    /**
     * @param        $input
     *
     * @return string
     */
    public static function parseImdbId($input): string
    {
        if (strpos($input, 'tt') !== 0) {
            $input = sprintf('tt%s', $input);
        }
        return $input;
    }
}
