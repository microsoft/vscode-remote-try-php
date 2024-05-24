<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Monitor;

use Joomla\Database\QueryMonitorInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Query monitor handling logging of queries.
 *
 * @since  2.0.0
 */
class LoggingMonitor implements QueryMonitorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Act on a query being started.
     *
     * @param   string         $sql          The SQL to be executed.
     * @param   object[]|null  $boundParams  List of bound params, used with the query.
     *                                       Each item is an object that holds: value, dataType
     * @return  void
     *
     * @since   2.0.0
     */
    public function startQuery(string $sql, ?array $boundParams = null): void
    {
        if ($this->logger) {
            // Add the query to the object queue.
            $this->logger->info(
                'Query Executed: {sql}',
                ['sql' => $sql, 'trace' => debug_backtrace()]
            );
        }
    }

    /**
     * Act on a query being stopped.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function stopQuery(): void
    {
        // Nothing to do
    }
}
