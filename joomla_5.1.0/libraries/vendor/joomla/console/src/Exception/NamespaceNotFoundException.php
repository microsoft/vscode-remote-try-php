<?php

/**
 * Part of the Joomla Framework Console Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Console\Exception;

use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Exception indicating a missing command namespace.
 *
 * @since  2.0.0
 */
class NamespaceNotFoundException extends CommandNotFoundException
{
}
