<?php

/**
 * Zmeni url na link
 *
 * @param string $apiURL
 * @return string
 */
function apiUrlToLink($apiURL)
{
    return str_replace('.json?limit=0', '',
        preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is",
            "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>",
            $apiURL));
}
