<?php
declare(strict_types = 1);

namespace Simbiat\Translit;

/**
 * Functions to convert strings
 */
class Convert
{
    private static string $url_unsafe = '\+\*\'\(\);/\?:@=&"<>#%{}\|\\\\\^~\[]`';
    private static array $romanizations = [];
    public static ?string $map = null;
    
    private static array $safe_for_files = [
        #Remove control characters and whitespaces except regular space
        '/[[:cntrl:]]/iu' => '',
        #Replace whitespace with regular space (hex 20)
        '/[\r\n\t\f\v\0\x{00A0}\x{2002}-\x{200B}\x{202F}\x{205F}\x{3000}\x{FEFF}]/iu' => ' ',
        #Replace characters with fullwidth alternatives
        '/</iu' => '＜',
        '/>/iu' => '＞',
        '/:/iu' => '：',
        '/"/iu' => '＂',
        '/\//iu' => '／',
        '/\\\\/iu' => '＼',
        '/\|/iu' => '｜',
        '/\?/iu' => '？',
        '/\*/iu' => '＊',
        #Replace Windows specific reserved words while retaining the case
        '/^(CON)(\..*)?$/u' => 'ＣＯＮ$2',
        '/^(con)(\..*)?$/u' => 'ｃｏｎ$2',
        '/^(COn)(\..*)?$/u' => 'ＣＯｎ$2',
        '/^(CoN)(\..*)?$/u' => 'ＣｏＮ$2',
        '/^(Con)(\..*)?$/u' => 'Ｃｏｎ$2',
        '/^(cON)(\..*)?$/u' => 'ｃＯＮ$2',
        '/^(coN)(\..*)?$/u' => 'ｃｏＮ$2',
        '/^(PRN)(\..*)?$/u' => 'ＰＲＮ$2',
        '/^(prn)(\..*)?$/u' => 'ｐｒｎ$2',
        '/^(PRn)(\..*)?$/u' => 'ＰＲｎ$2',
        '/^(PrN)(\..*)?$/u' => 'ＰｒＮ$2',
        '/^(Prn)(\..*)?$/u' => 'Ｐｒｎ$2',
        '/^(pRN)(\..*)?$/u' => 'ｐＲＮ$2',
        '/^(prN)(\..*)?$/u' => 'ｐｒＮ$2',
        '/^(AUX)(\..*)?$/u' => 'ＡＵＸ$2',
        '/^(aux)(\..*)?$/u' => 'ａｕｘ$2',
        '/^(AUx)(\..*)?$/u' => 'ＡＵｘ$2',
        '/^(AuX)(\..*)?$/u' => 'ＡｕＸ$2',
        '/^(Aux)(\..*)?$/u' => 'Ａｕｘ$2',
        '/^(aUX)(\..*)?$/u' => 'ａＵＸ$2',
        '/^(auX)(\..*)?$/u' => 'ａｕＸ$2',
        '/^(NUL)(\..*)?$/u' => 'ＮＵＬ$2',
        '/^(nul)(\..*)?$/u' => 'ｎｕｌ$2',
        '/^(NUl)(\..*)?$/u' => 'ＮＵｌ$2',
        '/^(NuL)(\..*)?$/u' => 'ＮｕＬ$2',
        '/^(Nul)(\..*)?$/u' => 'Ｎｕｌ$2',
        '/^(nUL)(\..*)?$/u' => 'ｎＵＬ$2',
        '/^(nuL)(\..*)?$/u' => 'ｎｕＬ$2',
        '/^(COM)(\d)(\..*)?$/u' => 'ＣＯＭ$2$3',
        '/^(com)(\d)(\..*)?$/u' => 'ｃｏｍ$2$3',
        '/^(COm)(\d)(\..*)?$/u' => 'ＣＯｍ$2$3',
        '/^(CoM)(\d)(\..*)?$/u' => 'ＣｏＭ$2$3',
        '/^(Com)(\d)(\..*)?$/u' => 'Ｃｏｍ$2$3',
        '/^(cOM)(\d)(\..*)?$/u' => 'ｃＯＭ$2$3',
        '/^(coM)(\d)(\..*)?$/u' => 'ｃｏＭ$2$3',
        '/^(LPT)(\d)(\..*)?$/u' => 'ＬＰＴ$2$3',
        '/^(lpt)(\d)(\..*)?$/u' => 'ｌｐｔ$2$3',
        '/^(LPt)(\d)(\..*)?$/u' => 'ＬＰｔ$2$3',
        '/^(LpT)(\d)(\..*)?$/u' => 'ＬｐＴ$2$3',
        '/^(Lpt)(\d)(\..*)?$/u' => 'Ｌｐｔ$2$3',
        '/^(lPT)(\d)(\..*)?$/u' => 'ｌＰＴ$2$3',
        '/^(lpT)(\d)(\..*)?$/u' => 'ｌｐＴ$2$3',
        #OSDATA is technically not prohibited by Windows, but creating a file or folder with such name in a certain folder can easily break it
        '/^osdata$/u' => 'ｏｓｄａｔａ',
        '/^Osdata$/u' => 'Ｏｓｄａｔａ',
        '/^oSdata$/u' => 'ｏＳｄａｔａ',
        '/^OSdata$/u' => 'ＯＳｄａｔａ',
        '/^osData$/u' => 'ｏｓＤａｔａ',
        '/^OsData$/u' => 'ＯｓＤａｔａ',
        '/^oSData$/u' => 'ｏＳＤａｔａ',
        '/^OSData$/u' => 'ＯＳＤａｔａ',
        '/^osdAta$/u' => 'ｏｓｄＡｔａ',
        '/^OsdAta$/u' => 'ＯｓｄＡｔａ',
        '/^oSdAta$/u' => 'ｏＳｄＡｔａ',
        '/^OSdAta$/u' => 'ＯＳｄＡｔａ',
        '/^osDAta$/u' => 'ｏｓＤＡｔａ',
        '/^OsDAta$/u' => 'ＯｓＤＡｔａ',
        '/^oSDAta$/u' => 'ｏＳＤＡｔａ',
        '/^OSDAta$/u' => 'ＯＳＤＡｔａ',
        '/^osdaTa$/u' => 'ｏｓｄａＴａ',
        '/^OsdaTa$/u' => 'ＯｓｄａＴａ',
        '/^oSdaTa$/u' => 'ｏＳｄａＴａ',
        '/^OSdaTa$/u' => 'ＯＳｄａＴａ',
        '/^osDaTa$/u' => 'ｏｓＤａＴａ',
        '/^OsDaTa$/u' => 'ＯｓＤａＴａ',
        '/^oSDaTa$/u' => 'ｏＳＤａＴａ',
        '/^OSDaTa$/u' => 'ＯＳＤａＴａ',
        '/^osdATa$/u' => 'ｏｓｄＡＴａ',
        '/^OsdATa$/u' => 'ＯｓｄＡＴａ',
        '/^oSdATa$/u' => 'ｏＳｄＡＴａ',
        '/^OSdATa$/u' => 'ＯＳｄＡＴａ',
        '/^osDATa$/u' => 'ｏｓＤＡＴａ',
        '/^OsDATa$/u' => 'ＯｓＤＡＴａ',
        '/^oSDATa$/u' => 'ｏＳＤＡＴａ',
        '/^OSDATa$/u' => 'ＯＳＤＡＴａ',
        '/^osdatA$/u' => 'ｏｓｄａｔＡ',
        '/^OsdatA$/u' => 'ＯｓｄａｔＡ',
        '/^oSdatA$/u' => 'ｏＳｄａｔＡ',
        '/^OSdatA$/u' => 'ＯＳｄａｔＡ',
        '/^osDatA$/u' => 'ｏｓＤａｔＡ',
        '/^OsDatA$/u' => 'ＯｓＤａｔＡ',
        '/^oSDatA$/u' => 'ｏＳＤａｔＡ',
        '/^OSDatA$/u' => 'ＯＳＤａｔＡ',
        '/^osdAtA$/u' => 'ｏｓｄＡｔＡ',
        '/^OsdAtA$/u' => 'ＯｓｄＡｔＡ',
        '/^oSdAtA$/u' => 'ｏＳｄＡｔＡ',
        '/^OSdAtA$/u' => 'ＯＳｄＡｔＡ',
        '/^osDAtA$/u' => 'ｏｓＤＡｔＡ',
        '/^OsDAtA$/u' => 'ＯｓＤＡｔＡ',
        '/^oSDAtA$/u' => 'ｏＳＤＡｔＡ',
        '/^OSDAtA$/u' => 'ＯＳＤＡｔＡ',
        '/^osdaTA$/u' => 'ｏｓｄａＴＡ',
        '/^OsdaTA$/u' => 'ＯｓｄａＴＡ',
        '/^oSdaTA$/u' => 'ｏＳｄａＴＡ',
        '/^OSdaTA$/u' => 'ＯＳｄａＴＡ',
        '/^osDaTA$/u' => 'ｏｓＤａＴＡ',
        '/^OsDaTA$/u' => 'ＯｓＤａＴＡ',
        '/^oSDaTA$/u' => 'ｏＳＤａＴＡ',
        '/^OSDaTA$/u' => 'ＯＳＤａＴＡ',
        '/^osdATA$/u' => 'ｏｓｄＡＴＡ',
        '/^OsdATA$/u' => 'ＯｓｄＡＴＡ',
        '/^oSdATA$/u' => 'ｏＳｄＡＴＡ',
        '/^OSdATA$/u' => 'ＯＳｄＡＴＡ',
        '/^osDATA$/u' => 'ｏｓＤＡＴＡ',
        '/^OsDATA$/u' => 'ＯｓＤＡＴＡ',
        '/^oSDATA$/u' => 'ｏＳＤＡＴＡ',
        '/^OSDATA$/u' => 'ＯＳＤＡＴＡ',
    
    ];
    
    /** Some more characters that you might want to replace with fullwidth alternatives, depending on how you use the files
     * @var array|string[]
     */
    private static array $safe_for_files_ext = [
        '/\[/iu' => '［',
        '/\]/iu' => '］',
        '/=/iu' => '＝',
        '/;/iu' => '；',
        '/,/iu' => '，',
        '/&/iu' => '＆',
        '/\$/iu' => '＄',
        '/#/iu' => '＃',
        '/\(/iu' => '（',
        '/\)/iu' => '）',
        '/\~/iu' => '～',
        '/\`/iu' => '｀',
        '/\'/iu' => '＇',
        '/\!/iu' => '！',
        '/\{/iu' => '｛',
        '/\}/iu' => '｝',
        '/%/iu' => '％',
        '/\+/iu' => '＋',
        '/‘/iu' => '＇',
        '/’/iu' => '＇',
        '/«/iu' => '＂',
        '/»/iu' => '＂',
        '/”/iu' => '＂',
        '/“/iu' => '＂',
    ];
    
    /**
     * Function transliterates lots of characters and makes a safe and pretty URL.
     *
     * @param string $string     String to process
     * @param string $whitespace Symbol to replace whitespace with
     * @param bool   $url_safe   If set to `true`, some characters will be removed as well, because they can "break" the URL. Some of them are valid for a URI, but they are not good for SEO links.
     *
     * @return string
     * @throws \JsonException
     */
    public static function prettyURL(string $string, string $whitespace = '-', bool $url_safe = true): string
    {
        $new_string = self::romanize($string);
        #Repalce whitespace
        $new_string = \preg_replace('/\s+/', $whitespace, $new_string);
        #Remove any other "forbidden" characters
        if ($url_safe) {
            $new_string = \preg_replace('[^a-zA-Z\d'.$whitespace.']', '', $new_string);
        } else {
            $new_string = \preg_replace('[^a-zA-Z\d'.self::$url_unsafe.$whitespace.']', '', $new_string);
        }
        return $new_string;
    }
    
    /**
     * Replace restricted characters or combinations
     * @param string $string   String to sanitize
     * @param bool   $extended If `true` - replace some special characters (common for programming languages) with fullwidth alternatives, so that the text will look similar but will not work as actual code
     * @param bool   $remove   If `true` - replace matches with empty string, instead of safe alternatives
     *
     * @return string
     */
    public static function safeFileName(string $string, bool $extended = true, bool $remove = false): string
    {
        #Replace special characters
        $string = \preg_replace(\array_keys(self::$safe_for_files), ($remove ? '' : self::$safe_for_files), $string);
        if ($extended) {
            $string = \preg_replace(\array_keys(self::$safe_for_files_ext), ($remove ? '' : self::$safe_for_files_ext), $string);
        }
        #Remove spaces and dots from the right (spaces on the left are possible
        return mb_rtrim(mb_rtrim(mb_rtrim($string, null, 'UTF-8'), '.', 'UTF-8'), null, 'UTF-8');
    }
    
    /**
     * Apply romanization logic to a string
     * @throws \JsonException
     */
    public static function romanize(string $string): string
    {
        self::ingestMap();
        #First, we apply built-in transliteration
        $replacement = \transliterator_transliterate('Any-Latin; Latin-ASCII;', $string, 0, -1);
        #Then we apply transliterations from the map
        return \preg_replace(\array_keys(self::$romanizations), self::$romanizations, $replacement);
    }
    
    /**
     * Helper to ingest and "cache" the map
     *
     * @return void
     * @throws \JsonException
     */
    private static function ingestMap(): void
    {
        #If no replacement list is provided, load the default one, but only if it has not been loaded already
        if (\count(self::$romanizations) === 0) {
            $map = self::$map ?? (__DIR__.'/map.json');
            $replacements = \array_merge(...\array_values(\json_decode(\file_get_contents($map), true, 512, \JSON_THROW_ON_ERROR)));
            $keys = \array_keys($replacements);
            #Wrap the keys in regex delimiters with the Unicode flag
            $keys = \array_map(static function ($item) {
                return '/'.$item.'/u';
            }, $keys);
            self::$romanizations = \array_combine($keys, $replacements);
        }
    }
}
