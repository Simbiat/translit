<?php
declare(strict_types = 1);

namespace Simbiat\Translit;

/**
 * Function to work with Unicode
 */
class Unicode
{
    /**
     * Cached Unicode blocks
     * @var array
     */
    private(set) static array $unicode_blocks = [];
    
    /**
     * List of character direction constants mapped to readable names
     * @var array
     */
    public const array DIRECTIONS = [
        'CHAR_DIRECTION_LEFT_TO_RIGHT' => \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT,
        'CHAR_DIRECTION_RIGHT_TO_LEFT' => \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT,
        'CHAR_DIRECTION_EUROPEAN_NUMBER' => \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER,
        'CHAR_DIRECTION_EUROPEAN_NUMBER_SEPARATOR' => \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER_SEPARATOR,
        'CHAR_DIRECTION_EUROPEAN_NUMBER_TERMINATOR' => \IntlChar::CHAR_DIRECTION_EUROPEAN_NUMBER_TERMINATOR,
        'CHAR_DIRECTION_ARABIC_NUMBER' => \IntlChar::CHAR_DIRECTION_ARABIC_NUMBER,
        'CHAR_DIRECTION_COMMON_NUMBER_SEPARATOR' => \IntlChar::CHAR_DIRECTION_COMMON_NUMBER_SEPARATOR,
        'CHAR_DIRECTION_BLOCK_SEPARATOR' => \IntlChar::CHAR_DIRECTION_BLOCK_SEPARATOR,
        'CHAR_DIRECTION_SEGMENT_SEPARATOR' => \IntlChar::CHAR_DIRECTION_SEGMENT_SEPARATOR,
        'CHAR_DIRECTION_WHITE_SPACE_NEUTRAL' => \IntlChar::CHAR_DIRECTION_WHITE_SPACE_NEUTRAL,
        'CHAR_DIRECTION_OTHER_NEUTRAL' => \IntlChar::CHAR_DIRECTION_OTHER_NEUTRAL,
        'CHAR_DIRECTION_LEFT_TO_RIGHT_EMBEDDING' => \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_EMBEDDING,
        'CHAR_DIRECTION_LEFT_TO_RIGHT_OVERRIDE' => \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_OVERRIDE,
        'CHAR_DIRECTION_RIGHT_TO_LEFT_ARABIC' => \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ARABIC,
        'CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING' => \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING,
        'CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE' => \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE,
        'CHAR_DIRECTION_POP_DIRECTIONAL_FORMAT' => \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_FORMAT,
        'CHAR_DIRECTION_DIR_NON_SPACING_MARK' => \IntlChar::CHAR_DIRECTION_DIR_NON_SPACING_MARK,
        'CHAR_DIRECTION_BOUNDARY_NEUTRAL' => \IntlChar::CHAR_DIRECTION_BOUNDARY_NEUTRAL,
        'CHAR_DIRECTION_FIRST_STRONG_ISOLATE' => \IntlChar::CHAR_DIRECTION_FIRST_STRONG_ISOLATE,
        'CHAR_DIRECTION_LEFT_TO_RIGHT_ISOLATE' => \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_ISOLATE,
        'CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE' => \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE,
        'CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE' => \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE,
        'CHAR_DIRECTION_CHAR_DIRECTION_COUNT' => \IntlChar::CHAR_DIRECTION_CHAR_DIRECTION_COUNT,
    ];
    
    /**
     * List of character type constants mapped to readable names
     * @var array
     */
    public const array TYPES = [
        'CHAR_CATEGORY_UNASSIGNED' => \IntlChar::CHAR_CATEGORY_UNASSIGNED,
        'CHAR_CATEGORY_GENERAL_OTHER_TYPES' => \IntlChar::CHAR_CATEGORY_GENERAL_OTHER_TYPES,
        'CHAR_CATEGORY_UPPERCASE_LETTER' => \IntlChar::CHAR_CATEGORY_UPPERCASE_LETTER,
        'CHAR_CATEGORY_LOWERCASE_LETTER' => \IntlChar::CHAR_CATEGORY_LOWERCASE_LETTER,
        'CHAR_CATEGORY_TITLECASE_LETTER' => \IntlChar::CHAR_CATEGORY_TITLECASE_LETTER,
        'CHAR_CATEGORY_MODIFIER_LETTER' => \IntlChar::CHAR_CATEGORY_MODIFIER_LETTER,
        'CHAR_CATEGORY_OTHER_LETTER' => \IntlChar::CHAR_CATEGORY_OTHER_LETTER,
        'CHAR_CATEGORY_NON_SPACING_MARK' => \IntlChar::CHAR_CATEGORY_NON_SPACING_MARK,
        'CHAR_CATEGORY_ENCLOSING_MARK' => \IntlChar::CHAR_CATEGORY_ENCLOSING_MARK,
        'CHAR_CATEGORY_COMBINING_SPACING_MARK' => \IntlChar::CHAR_CATEGORY_COMBINING_SPACING_MARK,
        'CHAR_CATEGORY_DECIMAL_DIGIT_NUMBER' => \IntlChar::CHAR_CATEGORY_DECIMAL_DIGIT_NUMBER,
        'CHAR_CATEGORY_LETTER_NUMBER' => \IntlChar::CHAR_CATEGORY_LETTER_NUMBER,
        'CHAR_CATEGORY_OTHER_NUMBER' => \IntlChar::CHAR_CATEGORY_OTHER_NUMBER,
        'CHAR_CATEGORY_SPACE_SEPARATOR' => \IntlChar::CHAR_CATEGORY_SPACE_SEPARATOR,
        'CHAR_CATEGORY_LINE_SEPARATOR' => \IntlChar::CHAR_CATEGORY_LINE_SEPARATOR,
        'CHAR_CATEGORY_PARAGRAPH_SEPARATOR' => \IntlChar::CHAR_CATEGORY_PARAGRAPH_SEPARATOR,
        'CHAR_CATEGORY_CONTROL_CHAR' => \IntlChar::CHAR_CATEGORY_CONTROL_CHAR,
        'CHAR_CATEGORY_FORMAT_CHAR' => \IntlChar::CHAR_CATEGORY_FORMAT_CHAR,
        'CHAR_CATEGORY_PRIVATE_USE_CHAR' => \IntlChar::CHAR_CATEGORY_PRIVATE_USE_CHAR,
        'CHAR_CATEGORY_SURROGATE' => \IntlChar::CHAR_CATEGORY_SURROGATE,
        'CHAR_CATEGORY_DASH_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_DASH_PUNCTUATION,
        'CHAR_CATEGORY_START_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_START_PUNCTUATION,
        'CHAR_CATEGORY_END_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_END_PUNCTUATION,
        'CHAR_CATEGORY_CONNECTOR_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_CONNECTOR_PUNCTUATION,
        'CHAR_CATEGORY_OTHER_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_OTHER_PUNCTUATION,
        'CHAR_CATEGORY_MATH_SYMBOL' => \IntlChar::CHAR_CATEGORY_MATH_SYMBOL,
        'CHAR_CATEGORY_CURRENCY_SYMBOL' => \IntlChar::CHAR_CATEGORY_CURRENCY_SYMBOL,
        'CHAR_CATEGORY_MODIFIER_SYMBOL' => \IntlChar::CHAR_CATEGORY_MODIFIER_SYMBOL,
        'CHAR_CATEGORY_OTHER_SYMBOL' => \IntlChar::CHAR_CATEGORY_OTHER_SYMBOL,
        'CHAR_CATEGORY_INITIAL_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_INITIAL_PUNCTUATION,
        'CHAR_CATEGORY_FINAL_PUNCTUATION' => \IntlChar::CHAR_CATEGORY_FINAL_PUNCTUATION,
        'CHAR_CATEGORY_CHAR_CATEGORY_COUNT' => \IntlChar::CHAR_CATEGORY_CHAR_CATEGORY_COUNT,
    ];
    
    /**
     * Get all Unicode characters in an array
     * @return array
     */
    public static function getAllUnicode(): array
    {
        $characters = [];
        for ($codepoint = 0; $codepoint <= 0x10FFFF; $codepoint++) {
            #Skip surrogates, since JSON will fail on them
            if ($codepoint >= 0xD800 && $codepoint <= 0xDFFF) {
                continue;
            }
            #Convert code point to UTF-8 character
            $char = mb_chr($codepoint, 'UTF-8');
            #Use character as a key, empty string as value
            if (\is_string($char)) {
                $characters[] = $char;
            }
        }
        return $characters;
    }
    
    /**
     * Generates 2 files: one with a list of all transliterations, one with all characters that are not transliterated by the library. This is mostly for testing and maintenance.
     * @return void
     * @throws \JsonException
     */
    public static function whatIsTransliterated(): void
    {
        $characters = self::getAllUnicode();
        $not_transliterated = [];
        $transliterated = [];
        #Since transliteration is done per character, it can take quite awhile to finish processing
        \ini_set('max_execution_time', '0');
        foreach ($characters as $character) {
            $character = (string)$character;
            $codepoint = \IntlChar::ord($character);
            $block_name = self::getBlockNameForCodepoint($codepoint) ?? 'Unknown';
            $char_name = \IntlChar::charName($codepoint);
            $hex = \mb_strtoupper(\dechex($codepoint), 'UTF-8');
            $for_regex = '\x{'.$hex.'}';
            $replacement = Convert::romanize($character);
            if ($character === $replacement && \preg_match('/^[a-zA-Z0-9]*$/', $replacement) !== 1) {
                $not_transliterated[$block_name][(self::hexRecommended($character) ? $for_regex : $character)] = $char_name;
            } else {
                $transliterated[$block_name][(self::hexRecommended($character) ? $for_regex : $character)] = $char_name.' (`'.$replacement.'`)';
            }
        }
        \file_put_contents(__DIR__.'/not_transliterated.json', \json_encode($not_transliterated, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT));
        \file_put_contents(__DIR__.'/transliterated.json', \json_encode($transliterated, \JSON_THROW_ON_ERROR | \JSON_UNESCAPED_UNICODE | \JSON_PRETTY_PRINT));
    }
    
    /**
     * Certain types of characters may need to be written as hex representation to avoid bad rendering of the JSONs in some editors
     * @param int|string $codepoint
     *
     * @return bool
     */
    private static function hexRecommended(int|string $codepoint): bool
    {
        return \in_array(\IntlChar::charType($codepoint), [
                \IntlChar::CHAR_CATEGORY_CONTROL_CHAR,
                \IntlChar::CHAR_CATEGORY_OTHER_LETTER,
                \IntlChar::CHAR_CATEGORY_COMBINING_SPACING_MARK,
                \IntlChar::CHAR_CATEGORY_NON_SPACING_MARK
            ], true) ||
            self::isRTL($codepoint) || self::isNonSpacing($codepoint) || self::isIsolate($codepoint) || self::isPop($codepoint);
    }
    
    /**
     * Wrapper for `\IntlChar::charDirection` to return a direction as a string, instead of integer
     * @param int|string $codepoint
     *
     * @return string
     */
    public static function charDirection(int|string $codepoint): string
    {
        return \array_search(\IntlChar::charDirection($codepoint), self::DIRECTIONS, true);
    }
    
    /**
     * Wrapper for `\IntlChar::charType` to return a type as a string, instead of integer
     * @param int|string $codepoint
     *
     * @return string
     */
    public static function charType(int|string $codepoint): string
    {
        return \array_search(\IntlChar::charType($codepoint), self::TYPES, true);
    }
    
    /**
     * Checks if provided codepoint or character is a Right-To-Left character
     * @param int|string $codepoint
     *
     * @return bool
     */
    public static function isRTL(int|string $codepoint): bool
    {
        #Get the direction of the character
        $direction = \IntlChar::charDirection($codepoint);
        #Check if the direction is RTL
        return \in_array($direction, [
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT,
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ARABIC,
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_EMBEDDING,
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_OVERRIDE,
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE,
            \IntlChar::CHAR_DIRECTION_ARABIC_NUMBER
        ], true);
    }
    
    /**
     * Checks if provided codepoint or character is a non-spacing mark
     * @param int|string $codepoint
     *
     * @return bool
     */
    public static function isNonSpacing(int|string $codepoint): bool
    {
        #Get the direction of the character
        $direction = \IntlChar::charDirection($codepoint);
        #Check if the direction is RTL
        return $direction === \IntlChar::CHAR_DIRECTION_DIR_NON_SPACING_MARK;
    }
    
    /**
     * Checks if provided codepoint or character is a direction "isolate"
     * @param int|string $codepoint
     *
     * @return bool
     */
    public static function isIsolate(int|string $codepoint): bool
    {
        #Get the direction of the character
        $direction = \IntlChar::charDirection($codepoint);
        #Check if the direction is RTL
        return \in_array($direction, [
            \IntlChar::CHAR_DIRECTION_FIRST_STRONG_ISOLATE,
            \IntlChar::CHAR_DIRECTION_LEFT_TO_RIGHT_ISOLATE,
            \IntlChar::CHAR_DIRECTION_RIGHT_TO_LEFT_ISOLATE,
            \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE
        ], true);
    }
    
    /**
     * Checks if provided codepoint or character is a direction mark
     * @param int|string $codepoint
     *
     * @return bool
     */
    public static function isPop(int|string $codepoint): bool
    {
        #Get the direction of the character
        $direction = \IntlChar::charDirection($codepoint);
        #Check if the direction is RTL
        return \in_array($direction, [
            \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_FORMAT,
            \IntlChar::CHAR_DIRECTION_POP_DIRECTIONAL_ISOLATE
        ], true);
    }
    
    /**
     * Get the latest list of blocks from Unicode.org in an array
     * @return array
     */
    public static function getUnicodeBlocks(): array
    {
        $block_file = \file_get_contents('https://www.unicode.org/Public/UCD/latest/ucd/Blocks.txt');
        $blocks = [];
        foreach (\explode("\n", $block_file) as $line) {
            if (\preg_match('/^([0-9A-F]+)\.\.([0-9A-F]+);\s(.+)$/', $line, $matches)) {
                [$start, $end, $name] = [\hexdec($matches[1]), \hexdec($matches[2]), \mb_trim($matches[3], null, 'UTF-8')];
                $blocks[] = \compact('start', 'end', 'name');
            }
        }
        self::$unicode_blocks = $blocks;
        return $blocks;
    }
    
    /**
     * Get the block name from codepoint
     * @param int $codepoint
     *
     * @return string|null
     */
    public static function getBlockNameForCodepoint(int $codepoint): ?string
    {
        if (\count(self::$unicode_blocks) === 0) {
            $blocks = self::getUnicodeBlocks();
        } else {
            $blocks = self::$unicode_blocks;
        }
        foreach ($blocks as $block) {
            if ($codepoint >= $block['start'] && $codepoint <= $block['end']) {
                return $block['name'];
            }
        }
        return null;
    }
}
