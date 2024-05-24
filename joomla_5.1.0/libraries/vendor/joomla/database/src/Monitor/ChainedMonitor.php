<?php

/**
 * Part of the Joomla Framework Database Package
 *
 * @copyright  Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Database\Monitor;

use Joomla\Database\QueryMonitorInterface;

/**
 * Chained query monitor allowing multiple monitors to be executed.
 *
 * @since  2.0.0
 */
class ChainedMonitor implements QueryMonitorInterface
{
    /**
     * The query monitors stored to this chain
     *
     * @var    QueryMonitorInterface[]
     * @since  2.0.0
     */
    private $monitors = [];

    /**
     * Register a monitor to the chain.
     *
     * @param   QueryMonitorInterface  $monitor  The monitor to add.
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function addMonitor(QueryMonitorInterface $monitor): void
    {
        $this->monitors[] = $monitor;
    }

    /**
     * Act on a query being started.
     *
     * @param   string         $sql           The SQL to be executed.
     * @param   object[]|null  $boundParams   List of bound params, used with the query.
     *                                        Each item is an object that holds: value, dataType
     *
     * @return  void
     *
     * @since   2.0.0
     */
    public function startQuery(string $sql, ?array $boundParams = null): void
    {
        foreach ($this->monitors as $monitor) {
            $monitor->startQuery($sql, $boundParams);
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
        foreach ($this->monitors as $monitor) {
            $monitor->stopQuery();
        }
    }
}
