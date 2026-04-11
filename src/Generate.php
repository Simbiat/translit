<?php
declare(strict_types = 1);

namespace Simbiat\StringHelpers;

/**
 * Functions to generate strings.
 */
class Generate
{
    
    /**
     * Generate all possible variations of a string where each character changes between lower and upper case (like `abc`, `Abc`, `aBc`, etc.)
     * @param string $string
     *
     * @return array
     */
    public static function caseVariations(string $string): array
    {
        $variations = [];
        $length = mb_strlen($string, 'UTF-8');
        #Calculate total number of combinations (2^n)
        $total_variations = 2 ** $length;
        #Iterate through each combination
        for ($iteration = 0; $iteration < $total_variations; $iteration++) {
            $variation = '';
            #Check each bit position
            for ($j_iteration = 0; $j_iteration < $length; $j_iteration++) {
                #Get the multibyte character from string
                $character = mb_substr($string, $j_iteration, 1, 'UTF-8');
                #If the j-th bit of i is set, convert the j-th character to uppercase (do not fully understand this, but it works)
                if (($iteration >> $j_iteration) & 1) {
                    $variation .= mb_strtoupper($character, 'UTF-8');
                } else {
                    $variation .= mb_strtolower($character, 'UTF-8');
                }
            }
            #Add the generated combination to the array
            $variations[] = $variation;
        }
        return \array_unique($variations);
    }
}
