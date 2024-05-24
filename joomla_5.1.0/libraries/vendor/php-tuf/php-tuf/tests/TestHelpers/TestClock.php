<?php

namespace Tuf\Tests\TestHelpers;

use Tuf\Helper\Clock;

/**
 * A test clock class that uses the time from the test fixtures as starting time.
 */
class TestClock extends Clock
{
    /**
     * The current time.
     *
     * Defaults to the same timestamp used in generate_fixtures.py to create
     * test fixtures.
     */
    private $time = 1577836800;

    /**
     * {@inheritdoc}
     */
    public function getCurrentTime(): int
    {
        // Increment the time simulate time passing between calls.
        $this->time++;
        return $this->time;
    }
}
