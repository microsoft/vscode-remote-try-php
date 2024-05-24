<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Text handling class.
 *
 * @since  1.0
 */
class Text
{
    /**
     * Language instance
     *
     * @var    Language
     * @since  1.0
     */
    private $language;

    /**
     * Constructor
     *
     * @param   Language  $language  Language instance to use in translations
     *
     * @since   2.0.0-alpha
     */
    public function __construct(Language $language)
    {
        $this->setLanguage($language);
    }

    /**
     * Retrieve the current Language instance
     *
     * @return  Language
     *
     * @since   2.0.0-alpha
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * Set the Language object
     *
     * @param   Language  $language  Language instance
     *
     * @return  $this
     *
     * @since   2.0.0-alpha
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Translates a string into the current language.
     *
     * @param   string   $string                The string to translate.
     * @param   array    $parameters            Array of parameters for the string
     * @param   boolean  $jsSafe                True to escape the string for JavaScript output
     * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     *
     * @return  string  The translated string or the key if $script is true
     *
     * @since   2.0.0-alpha
     */
    public function translate(string $string, array $parameters = [], bool $jsSafe = false, bool $interpretBackSlashes = true): string
    {
        $translated = $this->getLanguage()->translate($string, $jsSafe, $interpretBackSlashes);

        if (!empty($parameters)) {
            $translated = strtr($translated, $parameters);
        }

        return $translated;
    }

    /**
     * Translates a string into the current language.
     *
     * @param   string   $string                The string to translate.
     * @param   string   $alt                   The alternate option for global string
     * @param   array    $parameters            Array of parameters for the string
     * @param   mixed    $jsSafe                Boolean: Make the result javascript safe.
     * @param   boolean  $interpretBackSlashes  To interpret backslashes (\\=\, \n=carriage return, \t=tabulation)
     *
     * @return  string  The translated string or the key if $script is true
     *
     * @since   1.0
     */
    public function alt($string, $alt, array $parameters = [], $jsSafe = false, $interpretBackSlashes = true)
    {
        if ($this->getLanguage()->hasKey($string . '_' . $alt)) {
            return $this->translate($string . '_' . $alt, $parameters, $jsSafe, $interpretBackSlashes);
        }

        return $this->translate($string, $parameters, $jsSafe, $interpretBackSlashes);
    }

    /**
     * Pluralises a string in the current language
     *
     * The last argument can take an array of options to configure the call to `Joomla\Language\Language::translate()`:
     *
     * array(
     *   'jsSafe' => boolean,
     *   'interpretBackSlashes' =>boolean
     * )
     *
     * where:
     *
     * jsSafe is a boolean to specify whether to make the result JavaScript safe.
     * interpretBackSlashes is a boolean to specify whether backslashes are interpreted (\\ -> \, \n -> new line, \t -> tab character).
     *
     * @param   string   $string  The format string.
     * @param   integer  $n       The number of items
     *
     * @return  string  The translated string
     *
     * @note    This method can take a mixed number of arguments for the sprintf function
     * @since   1.0
     */
    public function plural($string, $n)
    {
        $lang  = $this->getLanguage();
        $args  = \func_get_args();
        $count = \count($args);

        // Try the key from the language plural potential suffixes
        $found    = false;
        $suffixes = $lang->getPluralSuffixes((int) $n);
        array_unshift($suffixes, (int) $n);

        foreach ($suffixes as $suffix) {
            $key = $string . '_' . $suffix;

            if ($lang->hasKey($key)) {
                $found = true;

                break;
            }
        }

        if (!$found) {
            // Not found so revert to the original.
            $key = $string;
        }

        if (\is_array($args[$count - 1])) {
            $args[0] = $lang->translate(
                $key,
                $args[$count - 1]['jsSafe'] ?? false,
                $args[$count - 1]['interpretBackSlashes'] ?? true
            );
        } else {
            $args[0] = $lang->translate($key);
        }

        return \sprintf(...$args);
    }

    /**
     * Passes a string thru a sprintf.
     *
     * The last argument can take an array of options to configure the call to `Joomla\Language\Language::translate()`:
     *
     * array(
     *   'jsSafe' => boolean,
     *   'interpretBackSlashes' =>boolean
     * )
     *
     * where:
     *
     * jsSafe is a boolean to specify whether to make the result JavaScript safe.
     * interpretBackSlashes is a boolean to specify whether backslashes are interpreted (\\ -> \, \n -> new line, \t -> tab character).
     *
     * @param   string  $string  The format string.
     *
     * @return  string|null  The translated string
     *
     * @note    This method can take a mixed number of arguments for the sprintf function
     * @since   1.0
     */
    public function sprintf($string)
    {
        $lang  = $this->getLanguage();
        $args  = \func_get_args();
        $count = \count($args);

        if (\is_array($args[$count - 1])) {
            $args[0] = $lang->translate(
                $string,
                $args[$count - 1]['jsSafe'] ?? false,
                $args[$count - 1]['interpretBackSlashes'] ?? true
            );
        } else {
            $args[0] = $lang->translate($string);
        }

        return \sprintf(...$args);
    }

    /**
     * Passes a string thru an printf.
     *
     * The last argument can take an array of options to configure the call to `Joomla\Language\Language::translate()`:
     *
     * array(
     *   'jsSafe' => boolean,
     *   'interpretBackSlashes' =>boolean
     * )
     *
     * where:
     *
     * jsSafe is a boolean to specify whether to make the result JavaScript safe.
     * interpretBackSlashes is a boolean to specify whether backslashes are interpreted (\\ -> \, \n -> new line, \t -> tab character).
     *
     * @param   string  $string  The format string.
     *
     * @return  string|null  The translated string
     *
     * @note    This method can take a mixed number of arguments for the printf function
     * @since   1.0
     */
    public function printf($string)
    {
        $lang  = $this->getLanguage();
        $args  = \func_get_args();
        $count = \count($args);

        if (\is_array($args[$count - 1])) {
            $args[0] = $lang->translate(
                $string,
                $args[$count - 1]['jsSafe'] ?? false,
                $args[$count - 1]['interpretBackSlashes'] ?? true
            );
        } else {
            $args[0] = $lang->translate($string);
        }

        return \printf(...$args);
    }
}
