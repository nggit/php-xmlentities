<?php

# NCR class
# Convert all Unicode characters and character references to numeric character references
# (c) 20161228 nggit

namespace Nggit\PHPXMLEntities;

class NCR
{
    public static function encode($str)
    {
        $str = html_entity_decode(
                   strtr($str,
                       array(
                           '&quot;' => '&amp;quot;',
                           '&apos;' => '&amp;#039;',
                           '&#039;' => '&amp;#039;',
                           '&lt;'   => '&amp;lt;',
                           '&gt;'   => '&amp;gt;',
                           '&amp;'  => '&amp;amp;'
                       )
                   ), ENT_NOQUOTES, 'UTF-8');
        $str_iso = utf8_decode(str_replace('?', '&#063;', $str)); // ISO-8859-1
        for ($i = 128; $i < 256; $i++) {
            $str_iso = str_replace(chr($i), '?', $str_iso); // ASCII
        }
        $encoded = '';
        while (($tok_pos = strpos($str_iso, '?')) !== false) {
            $byte_first = sprintf('%08b', ord($str[$tok_pos]));
            $zero_pos   = strpos($byte_first, '0');
            $char_len   = $zero_pos + !$zero_pos;
            $char       = substr($byte_first, $zero_pos + 1);
            for ($i = 1; $i < $char_len; $i++) {
                $byte_next = decbin(ord($str[$tok_pos + $i]));
                $char     .= substr($byte_next, strpos($byte_next, '0') + 1);
            }
            $encoded .= substr($str_iso, 0, $tok_pos) . '&#' . bindec($char) . ';';
            $str      = substr($str, $tok_pos + $char_len);
            $str_iso  = substr($str_iso, $tok_pos + 1);
        }
        return str_replace('&#063;', '?', $encoded) /* not required to replace */ . $str;
    }
}
