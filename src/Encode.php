<?php
declare(strict_types = 1);

namespace Simbiat\Translit;

/**
 * Functions to encode strings.
 */
class Encode
{
    
    /**
     * Encode string using base64url encoding
     * @param string $string
     *
     * @return string
     */
    public static function base64url(string $string): string
    {
        $string = \base64_encode($string);
        #Replace `+` with `-` and `/` with `_`
        $string = strtr($string, '+/', '-_');
        #Remove padding character from and return
        return mb_rtrim($string, '=', 'UTF-8');
    }
}
