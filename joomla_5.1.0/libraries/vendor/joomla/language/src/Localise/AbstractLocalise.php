<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language\Localise;

use Joomla\Language\LocaliseInterface;
use Joomla\Language\Transliterate;
use Joomla\String\StringHelper;

/**
 * Abstract localisation handler class
 *
 * @since  2.0.0-alpha
 */
abstract class AbstractLocalise implements LocaliseInterface
{
    /**
     * Transliterate function
     *
     * This method processes a string and replaces all accented UTF-8 characters by unaccented ASCII-7 equivalents.
     *
     * @param   string  $string  The string to transliterate.
     *
     * @return  string|boolean  The transliterated string or boolean false on a failure
     *
     * @since   2.0.0-alpha
     */
    public function transliterate($string)
    {
        $string = (new Transliterate())->utf8_latin_to_ascii($string);

        return StringHelper::strtolower($string);
    }

    /**
     * Returns an array of suffixes for plural rules.
     *
     * @param   integer  $count  The count number the rule is for.
     *
     * @return  string[]  The array of suffixes.
     *
     * @since   2.0.0-alpha
     */
    public function getPluralSuffixes($count)
    {
        return [(string) $count];
    }
}
