<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @copyright  Copyright (C) 2005 Richard Heyes (http://www.phpguru.org/). All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Stemmer;

use Joomla\Language\StemmerInterface;

/**
 * Porter English stemmer class.
 *
 * This class was adapted from one written by Richard Heyes.
 * See copyright and link information above.
 *
 * @since  1.0
 */
class Porteren implements StemmerInterface
{
    /**
     * An internal cache of stemmed tokens.
     *
     * @var    array
     * @since  1.0
     */
    protected $cache = [];

    /**
     * Regex for matching a consonant.
     *
     * @var    string
     * @since  1.4.0
     */
    private $regexConsonant = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

    /**
     * Regex for matching a vowel
     *
     * @var    string
     * @since  1.4.0
     */
    private $regexVowel = '(?:[aeiou]|(?<![aeiou])y)';

    /**
     * Method to stem a token and return the root.
     *
     * @param   string  $token  The token to stem.
     * @param   string  $lang   The language of the token.
     *
     * @return  string  The root token.
     *
     * @since   1.0
     */
    public function stem($token, $lang)
    {
        // Check if the token is long enough to merit stemming.
        if (\strlen($token) <= 2) {
            return $token;
        }

        // Check if the language is English or All.
        if ($lang !== 'en') {
            return $token;
        }

        // Stem the token if it is not in the cache.
        if (!isset($this->cache[$lang][$token])) {
            // Stem the token.
            $result = $token;
            $result = $this->step1ab($result);
            $result = $this->step1c($result);
            $result = $this->step2($result);
            $result = $this->step3($result);
            $result = $this->step4($result);
            $result = $this->step5($result);

            // Add the token to the cache.
            $this->cache[$lang][$token] = $result;
        }

        return $this->cache[$lang][$token];
    }

    /**
     * Step 1
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step1ab($word)
    {
        // Part a
        if (substr($word, -1) == 's') {
            $this->replace($word, 'sses', 'ss')
            || $this->replace($word, 'ies', 'i')
            || $this->replace($word, 'ss', 'ss')
            || $this->replace($word, 's', '');
        }

        // Part b
        if (substr($word, -2, 1) != 'e' || !$this->replace($word, 'eed', 'ee', 0)) {
            // First rule
            $v = $this->regexVowel;

            // Check ing and ed
            // Note use of && and OR, for precedence reasons
            if (
                preg_match("#$v+#", substr($word, 0, -3)) && $this->replace($word, 'ing', '')
                || preg_match("#$v+#", substr($word, 0, -2)) && $this->replace($word, 'ed', '')
            ) {
                // If one of above two test successful
                if (!$this->replace($word, 'at', 'ate') && !$this->replace($word, 'bl', 'ble') && !$this->replace($word, 'iz', 'ize')) {
                    // Double consonant ending
                    if ($this->doubleConsonant($word) && substr($word, -2) != 'll' && substr($word, -2) != 'ss' && substr($word, -2) != 'zz') {
                        $word = substr($word, 0, -1);
                    } elseif ($this->m($word) == 1 && $this->cvc($word)) {
                        $word .= 'e';
                    }
                }
            }
        }

        return $word;
    }

    /**
     * Step 1c
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step1c($word)
    {
        $v = $this->regexVowel;

        if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
            $this->replace($word, 'y', 'i');
        }

        return $word;
    }

    /**
     * Step 2
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step2($word)
    {
        switch (substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'ational', 'ate', 0)
                || $this->replace($word, 'tional', 'tion', 0);

                break;

            case 'c':
                $this->replace($word, 'enci', 'ence', 0)
                || $this->replace($word, 'anci', 'ance', 0);

                break;

            case 'e':
                $this->replace($word, 'izer', 'ize', 0);

                break;

            case 'g':
                $this->replace($word, 'logi', 'log', 0);

                break;

            case 'l':
                $this->replace($word, 'entli', 'ent', 0)
                || $this->replace($word, 'ousli', 'ous', 0)
                || $this->replace($word, 'alli', 'al', 0)
                || $this->replace($word, 'bli', 'ble', 0)
                || $this->replace($word, 'eli', 'e', 0);

                break;

            case 'o':
                $this->replace($word, 'ization', 'ize', 0)
                || $this->replace($word, 'ation', 'ate', 0)
                || $this->replace($word, 'ator', 'ate', 0);

                break;

            case 's':
                $this->replace($word, 'iveness', 'ive', 0)
                || $this->replace($word, 'fulness', 'ful', 0)
                || $this->replace($word, 'ousness', 'ous', 0)
                || $this->replace($word, 'alism', 'al', 0);

                break;

            case 't':
                $this->replace($word, 'biliti', 'ble', 0)
                || $this->replace($word, 'aliti', 'al', 0)
                || $this->replace($word, 'iviti', 'ive', 0);

                break;
        }

        return $word;
    }

    /**
     * Step 3
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step3($word)
    {
        switch (substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'ical', 'ic', 0);

                break;

            case 's':
                $this->replace($word, 'ness', '', 0);

                break;

            case 't':
                $this->replace($word, 'icate', 'ic', 0)
                || $this->replace($word, 'iciti', 'ic', 0);

                break;

            case 'u':
                $this->replace($word, 'ful', '', 0);

                break;

            case 'v':
                $this->replace($word, 'ative', '', 0);

                break;

            case 'z':
                $this->replace($word, 'alize', 'al', 0);

                break;
        }

        return $word;
    }

    /**
     * Step 4
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step4($word)
    {
        switch (substr($word, -2, 1)) {
            case 'a':
                $this->replace($word, 'al', '', 1);

                break;

            case 'c':
                $this->replace($word, 'ance', '', 1)
                || $this->replace($word, 'ence', '', 1);

                break;

            case 'e':
                $this->replace($word, 'er', '', 1);

                break;

            case 'i':
                $this->replace($word, 'ic', '', 1);

                break;

            case 'l':
                $this->replace($word, 'able', '', 1)
                || $this->replace($word, 'ible', '', 1);

                break;

            case 'n':
                $this->replace($word, 'ant', '', 1)
                || $this->replace($word, 'ement', '', 1)
                || $this->replace($word, 'ment', '', 1)
                || $this->replace($word, 'ent', '', 1);

                break;

            case 'o':
                if (substr($word, -4) == 'tion' || substr($word, -4) == 'sion') {
                    $this->replace($word, 'ion', '', 1);
                } else {
                    $this->replace($word, 'ou', '', 1);
                }

                break;

            case 's':
                $this->replace($word, 'ism', '', 1);

                break;

            case 't':
                $this->replace($word, 'ate', '', 1)
                || $this->replace($word, 'iti', '', 1);

                break;

            case 'u':
                $this->replace($word, 'ous', '', 1);

                break;

            case 'v':
                $this->replace($word, 'ive', '', 1);

                break;

            case 'z':
                $this->replace($word, 'ize', '', 1);

                break;
        }

        return $word;
    }

    /**
     * Step 5
     *
     * @param   string  $word  The token to stem.
     *
     * @return  string
     *
     * @since   1.0
     */
    private function step5($word)
    {
        // Part a
        if (substr($word, -1) == 'e') {
            if ($this->m(substr($word, 0, -1)) > 1) {
                $this->replace($word, 'e', '');
            } elseif ($this->m(substr($word, 0, -1)) == 1) {
                if (!$this->cvc(substr($word, 0, -1))) {
                    $this->replace($word, 'e', '');
                }
            }
        }

        // Part b
        if ($this->m($word) > 1 && $this->doubleConsonant($word) && substr($word, -1) == 'l') {
            $word = substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Replaces the first string with the second, at the end of the string. If third
     * arg is given, then the preceding string must match that m count at least.
     *
     * @param   string   $str    String to check
     * @param   string   $check  Ending to check for
     * @param   string   $repl   Replacement string
     * @param   integer  $m      Optional minimum number of m() to meet
     *
     * @return  boolean  Whether the $check string was at the end
     *                   of the $str string. True does not necessarily mean
     *                   that it was replaced.
     *
     * @since   1.0
     */
    private function replace(&$str, $check, $repl, $m = null)
    {
        $len = 0 - \strlen($check);

        if (substr($str, $len) == $check) {
            $substr = substr($str, 0, $len);

            if ($m === null || $this->m($substr) > $m) {
                $str = $substr . $repl;
            }

            return true;
        }

        return false;
    }

    /**
     * m() measures the number of consonant sequences in $str. if c is
     * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
     * presence,
     *
     * <c><v>       gives 0
     * <c>vc<v>     gives 1
     * <c>vcvc<v>   gives 2
     * <c>vcvcvc<v> gives 3
     *
     * @param   string  $str  The string to return the m count for
     *
     * @return  integer  The m count
     *
     * @since   1.0
     */
    private function m($str)
    {
        $c = $this->regexConsonant;
        $v = $this->regexVowel;

        $str = preg_replace("#^$c+#", '', $str);
        $str = preg_replace("#$v+$#", '', $str);

        preg_match_all("#($v+$c+)#", $str, $matches);

        return \count($matches[1]);
    }

    /**
     * Returns true/false as to whether the given string contains two
     * of the same consonant next to each other at the end of the string.
     *
     * @param   string  $str  String to check
     *
     * @return  boolean  Result
     *
     * @since   1.0
     */
    private function doubleConsonant($str)
    {
        $c = $this->regexConsonant;

        return preg_match("#$c{2}$#", $str, $matches) && $matches[0][0] === $matches[0][1];
    }

    /**
     * Checks for ending CVC sequence where second C is not W, X or Y
     *
     * @param   string  $str  String to check
     *
     * @return  boolean  Result
     *
     * @since   1.0
     */
    private function cvc($str)
    {
        $c = $this->regexConsonant;
        $v = $this->regexVowel;

        return preg_match("#($c$v$c)$#", $str, $matches)
            && \strlen($matches[1]) === 3
            && $matches[1][2] !== 'w'
            && $matches[1][2] !== 'x'
            && $matches[1][2] !== 'y';
    }
}
