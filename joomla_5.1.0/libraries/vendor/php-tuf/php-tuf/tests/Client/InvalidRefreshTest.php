<?php

namespace Tuf\Tests\Client;

use Tuf\Exception\RepoFileNotFound;
use Tuf\Tests\ClientTestBase;

/**
 * Tests refreshing the updater when the server is in an invalid state.
 */
class InvalidRefreshTest extends ClientTestBase
{
    /**
     * @testWith ["consistent"]
     *   ["inconsistent"]
     */
    public function testRefreshFromServerInInvalidState(string $fixtureVariant): void
    {
        $fixtureName = 'Simple/' . $fixtureVariant;

        $this->loadClientAndServerFilesFromFixture($fixtureName);
        $updater = $this->getUpdater();
        // This refresh should succeed.
        $updater->refresh();
        // Put the server-side repo into an invalid state.
        unset($this->serverFiles['timestamp.json']);
        // The updater is already refreshed, so this will return early, and
        // there should be no changes to the client-side repo.
        $updater->refresh();
        $this->assertMetadataVersions(static::getClientStartVersions($fixtureName), $this->clientStorage);
        // If we force a refresh, the invalid state of the server-side repo will
        // raise an exception.
        $this->expectException(RepoFileNotFound::class);
        $this->expectExceptionMessage('File timestamp.json not found.');
        $updater->refresh(true);
    }
}
