<?php
namespace MMHK\SMS\Traits;

trait Util
{
    /**
     * 添加電話號碼前綴
     *
     * @param $src
     * @return bool|string
     */
    public static function FixPhoneNumber($src) {
        //check hk phone
        if (strpos($src, '852') === 0 && strlen($src) == 11) {
            return $src;
        }
        //check chinese phone
        if (strpos($src, '86') === 0 && strlen($src) == 13) {
            return $src;
        }
        if (strlen($src) == 8) {
            return '852'.$src;
        }
        if (strlen($src) == 11) {
            return '86'.$src;
        }
        return false;
    }

    public static function convert($language, $cell){
        $str = "";
        preg_match_all("/[\x80-\xff]?./",$cell,$ar);

        switch ($language){
            case 0:
                foreach ($ar[0] as $v)
                    $str .= "&#".self::chinese_unicode(iconv("big5-hkscs","UTF-8",$v)).";";
                return $str;
                break;
            case 1:
                foreach ($ar[0] as $v)
                    $str .= "&#".self::chinese_unicode(iconv("gb2312","UTF-8",$v)).";";
                return $str;
                break;
            case 2:
                foreach (self::utf8_unicode($cell) as $v)
                    $str .= "&#".$v.";";
                return $str;
                break;
        }
    }

    public static function convert_back($language, $cell){
        $str = "";
        switch ($language){
            case 0:
                $str = preg_replace("|&#([0-9]{1,5});|", "\".iconv('UTF-8', 'big5-hkscs', unicode_utf8(\\1)).\"", $cell);
                $str = "\$str=\"$str\";";
                eval($str);
                return $str;
                break;
            case 1:
                $str = preg_replace("|&#([0-9]{1,5});|", "\".iconv('UTF-8', 'gb2312', unicode_utf8(\\1)).\"", $cell);
                $str = "\$str=\"$str\";";
                eval($str);
                return $str;
            case 2:
                $str = preg_replace("|&#([0-9]{1,5});|", "\".iconv('UTF-8', 'UTF-8', unicode_utf8(\\1)).\"", $cell);
                $str = "\$str=\"$str\";";
                eval($str);
                return $str;
                break;
        }
    }

    public static function chinese_unicode($c){
        switch (strlen($c)){
            case 1:
                return ord($c);
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                return $n;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                return $n;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                return $n;
        }
    }

    public static function utf8_unicode($str){
        $unicode = array();
        $values = array();
        $lookingFor = 1;

        for ($i = 0; $i < strlen($str); $i++){
            $thisValue = ord($str[$i]);

            if ($thisValue < 128)
                $unicode[] = $thisValue;
            else{
                if (count($values) == 0)
                    $lookingFor = ($thisValue < 224) ? 2 : 3;

                $values[] = $thisValue;

                if (count($values) == $lookingFor){
                    $number = ( $lookingFor == 3 ) ?
                        ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
                        ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

                    $unicode[] = $number;
                    $values = array();
                    $lookingFor = 1;
                }
            }
        }
        return $unicode;
    }

    public static function unicode_utf8($c){
        $str="";
        if ($c < 0x80){
            $str.=chr($c);
        } else if ($c < 0x800){
            $str.=chr(0xC0 | $c>>6);
            $str.=chr(0x80 | $c & 0x3F);
        } else if ($c < 0x10000){
            $str.=chr(0xE0 | $c>>12);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        } else if ($c < 0x200000){
            $str.=chr(0xF0 | $c>>18);
            $str.=chr(0x80 | $c>>12 & 0x3F);
            $str.=chr(0x80 | $c>>6 & 0x3F);
            $str.=chr(0x80 | $c & 0x3F);
        }
        return $str;
    }

    public static function unicode_get($str){
        $str = preg_replace("/&#/", "%26%23",$str);
        $str = preg_replace("/;/", "%3B",$str);
        return $str;
    }

    public static function strhex($string){
        $hex="";
        for ($i=0;$i<strlen($string);$i++)
            $hex.=(strlen(dechex(ord($string[$i])))<2)? "0".dechex(ord($string[$i])): dechex(ord($string[$i]));
        return $hex;
    }

    public static function hexstr($hex){
        $string="";
        for ($i=0;$i<strlen($hex)-1;$i+=2)
            $string.=chr(hexdec($hex[$i].$hex[$i+1]));
        return $string;
    }

    public static function name_exts($filename){
        $filename = strtolower($filename);
        $name = explode("[/\\.]", $filename);
        return $name;
    }
}