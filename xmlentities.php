<?php

# xmlentities (without mb_*, iconv, regex)
# Convert HTML named entities, Unicode characters to Numeric Character Reference
# (c) 20161228 nggit

class YourClass {

    // There are three xmlentities[1-3]
    // Rename to "xmlentities" the one you want to use
    // xmlentities3 may be slower but it will definitely convert all Unicode characters
    // Tags are allowed by default

    public static function xmlentities1($str) { // use standard PHP Functions
        $str = strtr($str,
                   array(
                       '&quot;' => '&amp;quot;',
                       '&apos;' => '&amp;#039;',
                       '&#039;' => '&amp;#039;',
                       '&lt;'   => '&amp;lt;',
                       '&gt;'   => '&amp;gt;',
                       '&amp;'  => '&amp;amp;'
                   ));
        $str = htmlentities($str, ENT_QUOTES, 'UTF-8', false);
        $t   = array();
        # begin allow tags
        $t['&quot;'] = '"';
        $t['&#039;'] = "'";
        $t['&lt;']   = '<';
        $t['&gt;']   = '>';
        # end allow tags
        $t['&amp;quot;'] = '&quot;';
        $t['&amp;#039;'] = '&#039;';
        $t['&amp;lt;']   = '&lt;';
        $t['&amp;gt;']   = '&gt;';
        $t['&amp;amp;']  = '&amp;';
        $str  = strtr($str, $t);
        $s    = explode('&', $str);
        $data = array_shift($s);
        foreach ($s as $e) {
            $ent = explode(';', $e, 2);
            $chr = html_entity_decode('&'.$ent[0].';', ENT_QUOTES, 'UTF-8');
            $len = strlen($chr);
            $u   = null;
            for ($i = 0; $i < $len; $i++) {
                $c  = str_pad(decbin(ord($chr[$i])), 8, '0', STR_PAD_LEFT);
                $u .= substr($c, strpos($c, '0') + 1);
            }
            $data .= '&#'.bindec($u).';'.$ent[1];
        }
        return $data;
    }

    public static function xmlentities2($str) { // require JSON (json_encode, json_decode)
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
        $json = json_encode($str);
        $json = str_replace('\\\\u', '\\u005cu', $json);
        $j    = explode('\\u', $json);
        $data = array_shift($j);
        foreach ($j as $u) {
            $hex   = substr($u, 0, 4);
            $data .= '&#x'.$hex.';'.substr($u, 4);
        }
        $data = str_replace('&#x005c;', '\\\\', $data);
        return json_decode($data);
    }

    public static function xmlentities3($str) { // require XML Parser (utf8_decode)
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
        $str = str_replace('?', '&#63;', $str);
        $iso = utf8_decode($str);
        static $chars = array();
        if (!$chars) {
            for ($i = 128; $i < 256; $i++) {
                $chars[] = chr($i);
            }
        }
        $ascii = str_replace($chars, '?', $iso);
        $a     = explode('?', $ascii);
        array_pop($a);
        $data = null;
        foreach ($a as $pre) {
            $current   = substr($str, strlen($pre));
            $firstByte = str_pad(decbin(ord($current[0])), 8, '0', STR_PAD_LEFT);
            $pos       = strpos($firstByte, '0');
            $len       = $pos ? $pos : 1;
            $u         = substr($firstByte, $pos + 1);
            for ($i = 1; $i < $len; $i++) {
                $nextByte = decbin(ord($current[$i]));
                $u       .= substr($nextByte, strpos($nextByte, '0') + 1);
            }
            $data .= $pre.'&#'.bindec($u).';';
            $str   = substr($current, $len);
        }
        $data .= $str;
        return str_replace('&#63;', '?', $data);
    }

}
