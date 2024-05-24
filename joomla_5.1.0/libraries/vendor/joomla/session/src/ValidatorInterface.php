<?php

/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Interface for validating a part of the session
 *
 * @since  2.0.0
 */
interface ValidatorInterface
{
    /**
     * Validates the session
     *
     * @param   boolean  $restart  Flag if the session should be restarted
     *
     * @return  void
     *
     * @since   2.0.0
     * @throws  Exception\InvalidSessionException
     */
    public function validate(bool $restart = false): void;
}
