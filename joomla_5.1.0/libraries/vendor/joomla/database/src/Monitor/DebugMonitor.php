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
 * Query monitor handling logging of queries.
 *
 * @since  2.0.0
 */
final class DebugMonitor implements QueryMonitorInterface
{
    /**
     * The log of executed SQL statements call stacks by the database driver.
     *
     * @var    array
     * @since  2.0.0
     */
    private $callStacks = [];

    /**
     * The log of executed SQL statements by the database driver.
     *
     * @var    array
     * @since  2.0.0
     */
    private $logs = [];

    /**
     * List of bound params, used with the query.
     *
     * @var    array
     * @since  2.0.0
     */
    private $boundParams = [];

    /**
     * The log of executed SQL statements memory usage (start and stop memory_get_usage) by the database driver.
     *
     * @var    array
     * @since  2.0.0
     */
    private $memoryLogs = [];

    /**
     * The log of executed SQL statements timings (start and stop microtimes) by the database driver.
     *
     * @var    array
     * @since  2.0.0
     */
    private $timings = [];

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
        $this->logs[]        = $sql;

        // Dereference bound parameters to prevent reporting wrong value when reusing the same query object.
        $this->boundParams[] = unserialize(serialize($boundParams));

        $this->callStacks[]  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $this->memoryLogs[]  = memory_get_usage();
        $this->timings[]     = microtime(true);
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
        $this->timings[]    = microtime(true);
        $this->memoryLogs[] = memory_get_usage();
    }

    /**
     * Get the logged call stacks.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getCallStacks(): array
    {
        return $this->callStacks;
    }

    /**
     * Get the logged queries.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Get the logged bound params.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getBoundParams(): array
    {
        return $this->boundParams;
    }

    /**
     * Get the logged memory logs.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getMemoryLogs(): array
    {
        return $this->memoryLogs;
    }

    /**
     * Get the logged timings.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public function getTimings(): array
    {
        return $this->timings;
    }
}
