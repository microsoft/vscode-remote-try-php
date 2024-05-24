<?php

namespace Tuf\Tests\Client;

use Tuf\Tests\ClientTestBase;

/**
 * Tests that the update is short-circuited if timestamp metadata is unchanged.
 */
class UnchangedTimestampTest extends ClientTestBase
{
    /**
     * @testWith ["consistent", "1.snapshot.json"]
     *   ["consistent", "1.targets.json"]
     *   ["inconsistent", "snapshot.json"]
     *   ["inconsistent", "targets.json"]
     */
    public function testUpdateShortCircuitsIfTimestampUnchanged(string $fixtureVariant, string $fileToDelete): void
    {
        // Use the Simple fixture because its server- and client-side timestamp
        // metadata files are identical.
        $this->loadClientAndServerFilesFromFixture("Simple/$fixtureVariant");

        // The removal of the file will cause an exception if the update doesn't
        // stop after downloading the unchanged timestamp metadata.
        unset($this->serverFiles[$fileToDelete]);
        $this->getUpdater()->refresh();
    }
}
