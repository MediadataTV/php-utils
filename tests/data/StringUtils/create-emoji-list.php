<?php

function getHexArray($grouped = true, array $skipStatus = []): array
{
    $emojiData = file_get_contents('https://www.unicode.org/Public/emoji/latest/emoji-test.txt');
    file_put_contents('emoji-test.txt', $emojiData);
    $lines           = explode("\n", $emojiData);
    $emojiArray      = [];
    $currentGroup    = 'main';
    $currentSubGroup = 'main';
    foreach ($lines as $line) {

        if (preg_match('/^# group: /', $line)) {
            $currentGroup = trim(str_replace('# group: ', '', $line));
        }

        if (preg_match('/^# subgroup: /', $line)) {
            $currentSubGroup = trim(str_replace('# subgroup: ', '', $line));
        }

        if (empty($line) || $line[0] === '#') {
            continue;
        }

        $parts        = explode(';', $line);
        $codepoint    = explode(' ', trim($parts[0]));
        $descriptions = explode('#', $parts[1]);
        $status       = trim($descriptions[0]);
        $emoji        = trim(preg_replace('/E\d{1,}\.\d{1,}.*/u', '', trim($descriptions[1])));
        $key          = sprintf('Group: %s - Subgroup: %s', $currentGroup, $currentSubGroup);
        if (!array_key_exists($key, $emojiArray)) {
            $emojiArray[$key] = [];
        }
        if (!in_array($status, $skipStatus, true)) {
            $emojiArray[$key][] = [
                'codepoint'       => $codepoint,
                'codepoint_count' => count($codepoint),
                'status'          => $status,
                'emoji'           => $emoji,
            ];
        }
    }
    if ($grouped === true) {
        foreach ($emojiArray as $group => &$data) {
            usort($data, static function ($a, $b) {
                return $b['codepoint_count'] <=> $a['codepoint_count'];
            });
        }
        unset($data);
    } else {
        $ungroupedEmojiArray = [];
        foreach ($emojiArray as $group => $data) {
            foreach ($data as $d) {
                $ungroupedEmojiArray[] = $d;
            }
        }
        usort($ungroupedEmojiArray, static function ($a, $b) {
            return $b['codepoint_count'] <=> $a['codepoint_count'];
        });
        $emojiArray = $ungroupedEmojiArray;
    }


    return $emojiArray;
}

/*function generateEmojiStringChunks(array $emojiArray, $chunkSize = 10): array
{
    $groups = [];
    foreach ($emojiArray as $groupName => $values) {
        $groupArray = [];
        foreach ($values as $v) {
            $groupArray[] .= $v['emoji'];
        }
        $groups[$groupName] = sprintf('%s', implode('|', $groupArray));
    }

    return $groups;
}

$emojiArray = generateEmojiStringChunks(getHexArray(), 50);*/

function generateEmojiRegexesChunks(array $emojiArray, $grouped = true, $chunkSize = 50): array
{
    $groups = [];
    if (!$grouped) {
        $emojiArray = array_chunk($emojiArray, $chunkSize);
    }
    foreach ($emojiArray as $groupName => $values) {
        $groupArray = [];
        foreach ($values as $v) {
            $groupArray[] = sprintf('(%s)', implode('', array_map(static function ($codepoint) {
                return sprintf('\x{%s}', $codepoint);
            }, $v['codepoint'])));
        }
        $groups[$groupName] = sprintf('%s', implode('|', $groupArray));
    }

    /* } else {

         foreach ($chunks as $idx => $chunks) {
             $groupArray[] = sprintf('(%s)', implode('', array_map(static function ($codepoint) {
                 return sprintf('\x{%s}', $codepoint);
             }, $c['codepoint'])));

             $groups[$idx] = sprintf('%s', implode('|', $groupArray));
         }
     }*/

    return $groups;
}

$grouped = false;

$emojiArray = generateEmojiRegexesChunks(getHexArray($grouped), $grouped);
array_walk($emojiArray, static function (&$value, $key) {
    $value = sprintf("'%s' => '%s',\n", trim($key), trim($value));
});
echo sprintf("\$emojiChunks = [\n%s\n];", implode('', $emojiArray));
