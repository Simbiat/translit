<?php
declare(strict_types = 1);

namespace Simbiat\Translit;

/**
 * Functions to decode strings.
 */
class Decode
{
    
    /**
     * Decode string encoded with base64url encoding
     * @param string $string
     *
     * @return string
     */
    public static function base64url(string $string): string
    {
        #Replace `-` with `+` and `_` with `/`
        $string = strtr($string, '-_', '+/');
        #Decode Base64 string and return the original data
        return \base64_decode($string, true);
    }
}
