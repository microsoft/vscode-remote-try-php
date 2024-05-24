<?php

/**
 * Part of the Joomla Framework Application Package
 *
 * @copyright  (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Application;

/**
 * Joomla Framework Application Interface
 *
 * @since  2.0.0
 */
interface ApplicationInterface
{
    /**
     * Method to close the application.
     *
     * @param   integer  $code  The exit code (optional; default is 0).
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function close($code = 0);

    /**
     * Execute the application.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function execute();
}
