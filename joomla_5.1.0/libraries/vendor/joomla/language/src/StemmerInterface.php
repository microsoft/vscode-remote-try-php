<?php

/**
 * Part of the Joomla Framework Language Package
 *
 * @copyright  Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Language;

/**
 * Stemmer interface.
 *
 * @since  1.4.0
 */
interface StemmerInterface
{
    /**
     * Method to stem a token and return the root.
     *
     * @param   string  $token  The token to stem.
     * @param   string  $lang   The language of the token.
     *
     * @return  string  The root token.
     *
     * @since   1.4.0
     */
    public function stem($token, $lang);
}
