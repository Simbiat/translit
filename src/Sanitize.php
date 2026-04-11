<?php
declare(strict_types = 1);

namespace Simbiat\StringHelpers;

/**
 * Functions to decode strings.
 */
class Sanitize
{
    
    /**
     * Validate if a string is valid for use as a database identifier (or part of it), such as database name, table name, index name, etc.
     *
     * @param string $string      String to validate
     * @param bool   $allow_empty Whether string is allowed to be empty (useful, when validating parts of string)
     * @param int    $max_length  Maximum length of the string. Default is 64, which is maximum length for most identifier in MySQL/MariaDB.
     *
     * @return bool
     */
    public static function dbName(string $string, bool $allow_empty = false, int $max_length = 64): bool
    {
        return \preg_match('/^[\w\-]{'.($allow_empty ? 0 : 1).','.$max_length.'}$/u', $string) === 1;
    }
    
    /**
     * Check if a string is empty or consists of only whitespace and/or control characters
     * @param string $string
     *
     * @return bool
     */
    public static function whiteString(string $string): bool
    {
        return \preg_match('/^\s*|\p{C}$/u', $string) === 1;
    }
}
