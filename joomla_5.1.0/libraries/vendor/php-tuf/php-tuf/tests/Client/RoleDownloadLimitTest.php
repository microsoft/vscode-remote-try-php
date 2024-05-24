<?php

namespace Tuf\Tests\Client;

use Tuf\Client\Updater;
use Tuf\Exception\NotFoundException;
use Tuf\Tests\ClientTestBase;

/**
 * Tests that there is a limit on the number of roles that can be downloaded.
 */
class RoleDownloadLimitTest extends ClientTestBase
{
    /**
     * Tests for enforcement of maximum number of roles limit.
     *
     * § 5.6.7.1
     *
     * @testWith ["consistent"]
     *   ["inconsistent"]
     */
    public function testRoleDownloadsAreLimited(string $fixtureVariant): void
    {
        $this->loadClientAndServerFilesFromFixture("NestedDelegated/$fixtureVariant");

        $fileName = 'level_1_2_terminating_3_target.txt';

        // Ensure the file can found if the maximum role limit is 100.
        $testFileContents = $this->serverFiles[$fileName];
        self::assertNotEmpty($testFileContents);
        self::assertSame($testFileContents, $this->getUpdater()->download($fileName)->wait()->getContents());

        // Ensure the file can not found if the maximum role limit is 3.
        self::expectException(NotFoundException::class);
        self::expectExceptionMessage("Target not found: $fileName");
        $this->getUpdater(LimitRolesTestUpdater::class)->download($fileName);
    }
}
// @codingStandardsIgnoreStart

/**
 * An updater to test the enforcement of MAXIMUM_TARGET_ROLES.
 */
class LimitRolesTestUpdater extends Updater
{
    /**
     * {@inheritdoc}
     */
    const MAXIMUM_TARGET_ROLES = 3;
}
// @codingStandardsIgnoreEnd
