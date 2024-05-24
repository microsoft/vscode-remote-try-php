<?php

namespace Tuf\Tests\Client;

use Tuf\CanonicalJsonTrait;
use Tuf\Client\Repository;
use Tuf\Exception\DownloadSizeException;
use Tuf\Exception\MetadataException;
use Tuf\Exception\NotFoundException;
use Tuf\Exception\Attack\SignatureThresholdException;
use Tuf\Exception\RepoFileNotFound;
use Tuf\Exception\TufException;
use Tuf\Tests\ClientTestBase;
use Tuf\Tests\TestHelpers\UtilsTrait;

/**
 * Base class for testing the client update workflow.
 */
abstract class UpdaterTest extends ClientTestBase
{
    use CanonicalJsonTrait;
    use UtilsTrait;

    /**
     * Tests that TUF transparently verifies targets signed by delegated roles.
     *
     * @param string $fixtureName
     *   The name of the fixture to test with.
     * @param string $target
     *   The target file to download.
     * @param array $expectedFileVersions
     *   The expected client versions after the download.
     *
     * @todo Add test coverage delegated roles that then delegate to other roles in
     *   https://github.com/php-tuf/php-tuf/issues/142
     *
     * @covers ::download
     *
     * § 5.7.3
     *
     * @dataProvider providerVerifiedDelegatedDownload
     *
     * @testdox Verify delegated target $target from $fixtureName
     */
    public function testVerifiedDelegatedDownload(string $fixtureName, string $target, array $expectedFileVersions): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);

        $testFilePath = static::getFixturePath($fixtureName, "server/targets/$target", false);
        $testFileContents = file_get_contents($testFilePath);
        self::assertNotEmpty($testFileContents);
        $this->assertSame($testFileContents, $this->getUpdater()->download($target)->wait()->getContents());
        // Ensure that client downloads only the delegated role JSON files that
        // are needed to find the metadata for the target.
        $this->assertMetadataVersions($expectedFileVersions, $this->clientStorage);
    }

    public function providerVerifiedDelegatedDownload(): array
    {
        return [
            // Test cases using the NestedDelegated fixture
            'level_1_target.txt' => [
                'NestedDelegated',
                'level_1_target.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => null,
                    'level_3' => null,
                ],
            ],
            'level_1_2_target.txt' => [
                'NestedDelegated',
                'level_1_2_target.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => 1,
                    'level_2_terminating' => null,
                    'level_3' => null,
                ],
            ],
            'level_1_2_terminating_findable.txt' => [
                'NestedDelegated',
                'level_1_2_terminating_findable.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => 1,
                    'level_2_terminating' => 1,
                    'level_3' => null,
                ],
            ],
            'level_1_2_3_below_non_terminating_target.txt' => [
                'NestedDelegated',
                'level_1_2_3_below_non_terminating_target.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => 1,
                    'level_2_terminating' => null,
                    'level_3' => 1,
                ],
            ],
            // Roles delegated from a terminating role are evaluated.
            // See § 5.6.7.2.1 and 5.6.7.2.2.
            'level_1_2_terminating_3_target.txt' => [
                'NestedDelegated',
                'level_1_2_terminating_3_target.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => 1,
                    'level_2_terminating' => 1,
                    'level_3' => null,
                    'level_3_below_terminated' => 1,
                ],
            ],
            // A terminating role only has an effect if the target path matches
            // the role, otherwise the role is not evaluated.
            // Roles after (i.e., next to) a terminating delegation, where the
            // target path does match not the terminating role, are not
            // evaluated.
            // See § 5.6.7.2.1 and 5.6.7.2.2.
            'level_1_2a_terminating_plus_1_more_findable.txt' => [
                'NestedDelegated',
                'level_1_2a_terminating_plus_1_more_findable.txt',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 2,
                    'level_2' => null,
                    'level_2_terminating' => 1,
                    'level_3' => 1,
                    'level_3_below_terminated' => 1,
                ],
            ],
            // Test cases using the 'TerminatingDelegation' fixture set.
            'TerminatingDelegation targets.txt' => [
                'TerminatingDelegation',
                'targets.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => null,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'TerminatingDelegation a.txt' => [
                'TerminatingDelegation',
                'a.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'TerminatingDelegation b.txt' => [
                'TerminatingDelegation',
                'b.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'TerminatingDelegation c.txt' => [
                'TerminatingDelegation',
                'c.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'TerminatingDelegation d.txt' => [
                'TerminatingDelegation',
                'd.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => null,
                    'f' => null,
                ],
            ],
            // Test cases using the 'TopLevelTerminating' fixture set.
            'TopLevelTerminating a.txt' => [
                'TopLevelTerminating',
                'a.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => null,
                ],
            ],
            // Test cases using the 'NestedTerminatingNonDelegatingDelegation' fixture set.
            'NestedTerminatingNonDelegatingDelegation a.txt' => [
                'NestedTerminatingNonDelegatingDelegation',
                'a.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                ],
            ],
            'NestedTerminatingNonDelegatingDelegation b.txt' => [
                'NestedTerminatingNonDelegatingDelegation',
                'b.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => null,
                    'd' => null,
                ],
            ],
            // Test using the ThreeLevelDelegation fixture set.
            'ThreeLevelDelegation targets.txt' => [
                'ThreeLevelDelegation',
                'targets.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => null,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation a.txt' => [
                'ThreeLevelDelegation',
                'a.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation b.txt' => [
                'ThreeLevelDelegation',
                'b.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation c.txt' => [
                'ThreeLevelDelegation',
                'c.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation d.txt' => [
                'ThreeLevelDelegation',
                'd.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation e.txt' => [
                'ThreeLevelDelegation',
                'e.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => 1,
                    'f' => null,
                ],
            ],
            'ThreeLevelDelegation f.txt' => [
                'ThreeLevelDelegation',
                'f.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => 1,
                    'f' => 1,
                ],
            ],
        ];
    }

    /**
     * Tests that improperly delegated targets will produce exceptions.
     *
     * @param string $fixtureName
     * @param string $fileName
     * @param array $expectedFileVersions
     *
     * @dataProvider providerDelegationErrors
     *
     * § 5.6.7.2.1
     * § 5.6.7.2.2
     * § 5.7.2
     */
    public function testDelegationErrors(string $fixtureName, string $fileName, array $expectedFileVersions): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        try {
            $this->getUpdater()->download($fileName);
        } catch (NotFoundException $exception) {
            self::assertEquals("Target not found: $fileName", $exception->getMessage());
            $this->assertMetadataVersions($expectedFileVersions, $this->clientStorage);
            return;
        }
        self::fail('NotFoundException not thrown.');
    }

    /**
     * Data provider for testDelegationErrors().
     *
     * The files used in these test cases are setup in the Python class
     * generate_fixtures.NestedDelegatedErrors().
     *
     * @return \string[][]
     */
    public function providerDelegationErrors(): array
    {
        return [
            // Test using the NestedDelegatedErrors fixture set.
            // 'level_a.txt' is added via the 'unclaimed' role but this role has
            // `paths: ['level_1_*.txt']` which does not match the file name.
            'no path match' => [
                'NestedDelegatedErrors',
                'level_a.txt',
                [
                    'timestamp' => 6,
                    'snapshot' => 6,
                    'targets' => 6,
                    // The client does not update the 'unclaimed.json' file because
                    // the target file does not match the 'paths' property for the role.
                    'unclaimed' => 1,
                    'level_2' => null,
                    'level_2_after_terminating' => null,
                    'level_2_terminating' => null,
                    'level_3' => null,
                    'level_3_below_terminated' => null,
                ],
            ],
            // 'level_1_3_target.txt' is added via the 'level_2' role which has
            // `paths: ['level_1_2_*.txt']`. The 'level_2' role is delegated from the
            // 'unclaimed' role which has `paths: ['level_1_*.txt']`. The file matches
            // for the 'unclaimed' role but does not match for the 'level_2' role.
            'matches parent delegation' => [
                'NestedDelegatedErrors',
                'level_1_3_target.txt',
                [
                    'timestamp' => 6,
                    'snapshot' => 6,
                    'targets' => 6,
                    'unclaimed' => 3,
                    'level_2' => null,
                    'level_2_after_terminating' => null,
                    'level_2_terminating' => null,
                    'level_3' => null,
                    'level_3_below_terminated' => null,
                ],
            ],
            // 'level_2_unfindable.txt' is added via the 'level_2_error' role which has
            // `paths: ['level_2_*.txt']`. The 'level_2_error' role is delegated from the
            // 'unclaimed' role which has `paths: ['level_1_*.txt']`. The file matches
            // for the 'level_2_error' role but does not match for the 'unclaimed' role.
            // No files added via the 'level_2_error' role will be found because its
            // 'paths' property is incompatible with the its parent delegation's
            // 'paths' property.
            'delegated path does not match parent' => [
                'NestedDelegatedErrors',
                'level_2_unfindable.txt',
                [
                    'timestamp' => 6,
                    'snapshot' => 6,
                    'targets' => 6,
                    // The client does not update the 'unclaimed.json' file because
                    // the target file does not match the 'paths' property for the role.
                    'unclaimed' => 1,
                    'level_2' => null,
                    'level_2_after_terminating' => null,
                    'level_2_terminating' => null,
                    'level_3' => null,
                    'level_3_below_terminated' => null,
                ],
            ],
            // 'level_1_2_terminating_plus_1_more_unfindable.txt' is added via role
            // 'level_2_after_terminating_match_terminating_path' which is delegated from role at the same level as 'level_2_terminating'
            'delegated path does not match role' => [
                'NestedDelegatedErrors',
                'level_1_2_terminating_plus_1_more_unfindable.txt',
                [
                    'timestamp' => 6,
                    'snapshot' => 6,
                    'targets' => 6,
                    // The client does update the 'unclaimed.json' file because
                    // the target file does match the 'paths' property for the role.
                    'unclaimed' => 3,
                    'level_2' => 2,
                    'level_2_after_terminating' => null,
                    'level_2_terminating' => null,
                    'level_3' => null,
                    'level_3_below_terminated' => null,
                ],
            ],
            // 'level_1_2_terminating_plus_1_more_unfindable.txt' is added via role
            // 'level_2_after_terminating_match_terminating_path' which is delegated from role at the same level as 'level_2_terminating'
            //  but added after 'level_2_terminating'.
            // Because 'level_2_terminating' is a terminating role its own delegations are evaluated but no other
            // delegations are evaluated after it.
            // See § 5.6.7.2.1 and 5.6.7.2.2.
            'delegation is after terminating delegation' => [
                'NestedDelegatedErrors',
                'level_1_2_terminating_plus_1_more_unfindable.txt',
                [
                    'timestamp' => 6,
                    'snapshot' => 6,
                    'targets' => 6,
                    'unclaimed' => 3,
                    'level_2' => 2,
                    'level_2_after_terminating' => null,
                    'level_2_terminating' => null,
                    'level_3' => null,
                    'level_3_below_terminated' => null,
                ],
            ],
            // Test using the TerminatingDelegation fixture set.
            'TerminatingDelegation e.txt' => [
                'TerminatingDelegation',
                'e.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => null,
                    'f' => null,
                ],
            ],
            'TerminatingDelegation f.txt' => [
                'TerminatingDelegation',
                'f.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => null,
                    'f' => null,
                ],
            ],
            // Test cases using the 'TopLevelTerminating' fixture set.
            'TopLevelTerminating b.txt' => [
                'TopLevelTerminating',
                'b.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => null,
                ],
            ],
            // Test cases using the 'NestedTerminatingNonDelegatingDelegation' fixture set.
            'NestedTerminatingNonDelegatingDelegation c.txt' => [
                'NestedTerminatingNonDelegatingDelegation',
                'c.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => null,
                    'd' => null,
                ],
            ],
            'NestedTerminatingNonDelegatingDelegation d.txt' => [
                'NestedTerminatingNonDelegatingDelegation',
                'd.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => null,
                    'd' => null,
                ],
            ],
            // Test cases using the 'ThreeLevelDelegation' fixture set.
            // A search for non existent target should that matches the paths
            // should search the complete tree.
            'ThreeLevelDelegation z.txt' => [
                'ThreeLevelDelegation',
                'z.txt',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => 1,
                    'b' => 1,
                    'c' => 1,
                    'd' => 1,
                    'e' => 1,
                    'f' => 1,
                ],
            ],
            // A search for non existent target that does match the paths
            // should not search any of the tree.
            'ThreeLevelDelegation z.zip' => [
                'ThreeLevelDelegation',
                'z.zip',
                [
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                    'a' => null,
                    'b' => null,
                    'c' => null,
                    'd' => null,
                    'e' => null,
                    'f' => null,
                ],
            ],
        ];
    }

    /**
     * Tests refreshing the repository.
     *
     * @param string $fixtureName
     *   The fixtures set to use.
     * @param array $expectedUpdatedVersions
     *   The expected updated versions.
     *
     * @dataProvider providerRefreshRepository
     *
     * @testdox Refresh $fixtureName repository
     */
    public function testRefreshRepository(string $fixtureName, array $expectedUpdatedVersions): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        $expectedStartVersion = static::getClientStartVersions($fixtureName);

        $this->assertTrue($this->getUpdater()->refresh());
        // Confirm the local version are updated to the expected versions.
        // § 5.3.8
        // § 5.4.5
        // § 5.5.7
        // § 5.6.6
        $this->assertMetadataVersions($expectedUpdatedVersions, $this->clientStorage);

        // Create another version of the client that only starts with the root.json file.
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        foreach (array_keys($expectedStartVersion) as $role) {
            if ($role !== 'root') {
                // Change the expectation that client will not start with any files other than root.json.
                $expectedStartVersion[$role] = null;
                // Remove all files except root.json.
                $this->clientStorage->delete($role);
            }
        }
        $this->assertMetadataVersions($expectedStartVersion, $this->clientStorage);
        $this->assertTrue($this->getUpdater()->refresh());
        // Confirm that if we start with only root.json all of the files still
        // update to the expected versions.

        foreach ($expectedUpdatedVersions as $role => $expectedUpdatedVersion) {
            if (!in_array($role, ['root', 'timestamp', 'snapshot', 'targets'])) {
                // Any delegated role metadata files are not fetched during refresh.
                $expectedUpdatedVersions[$role] = null;
            }
        }
        $this->assertMetadataVersions($expectedUpdatedVersions, $this->clientStorage);
    }

    /**
     * Dataprovider for testRefreshRepository().
     *
     * @return mixed[]
     *   The data set for testRefreshRepository().
     */
    public function providerRefreshRepository(): array
    {
        return [
            'Delegated' => [
                'Delegated',
                [
                    'timestamp' => 4,
                    'snapshot' => 4,
                    'targets' => 4,
                    'unclaimed' => 1,
                ],
            ],
            'Simple' => [
                'Simple',
                [
                    'root' => 1,
                    'timestamp' => 1,
                    'snapshot' => 1,
                    'targets' => 1,
                ],
            ],
            'NestedDelegated' => [
                'NestedDelegated',
                [
                    'timestamp' => 5,
                    'snapshot' => 5,
                    'targets' => 5,
                    'unclaimed' => 1,
                    'level_2' => null,
                    'level_3' => null,
                ],
            ],
        ];
    }

    /**
     * Tests that exceptions are thrown when metadata files are not valid.
     *
     * @param string $fileToChange
     *   The file to change.
     * @param array $keys
     *   The nested keys of the element to change.
     * @param mixed $newValue
     *   The new value to set.
     * @param \Exception $expectedException
     *   The expected exception.
     * @param array $expectedUpdatedVersions
     *   The expected repo file version after refresh attempt.
     *
     * @dataProvider providerExceptionForInvalidMetadata
     *
     * @testdox Invalid metadata in $fileToChange raises an exception
     */
    public function testExceptionForInvalidMetadata(string $fileToChange, array $keys, $newValue, \Exception $expectedException, array $expectedUpdatedVersions): void
    {
        $this->loadClientAndServerFilesFromFixture('Delegated');

        $data = static::decodeJson($this->serverFiles[$fileToChange]);
        static::nestedChange($keys, $data, $newValue);
        $this->serverFiles[$fileToChange] = static::encodeJson($data);

        try {
            $this->getUpdater()->refresh();
        } catch (TufException $exception) {
            $this->assertEquals($expectedException, $exception);
            $this->assertMetadataVersions($expectedUpdatedVersions, $this->clientStorage);
            return;
        }
        $this->fail('No exception thrown. Expected: ' . get_class($expectedException));
    }

    /**
     * Data provider for testExceptionForInvalidMetadata().
     *
     * @return mixed[]
     *   The test cases for testExceptionForInvalidMetadata().
     */
    public function providerExceptionForInvalidMetadata(): array
    {
        return [
            'add key to root.json' => [
                // § 5.3.4
                '3.root.json',
                ['signed', 'newkey'],
                'new value',
                new SignatureThresholdException('Signature threshold not met on root'),
                [
                    'root' => 2,
                    'timestamp' => 2,
                    'snapshot' => 2,
                    'targets' => 2,
                ],
            ],
            'add key to timestamp.json' => [
                // § 5.3.11
                // § 5.4.2
                'timestamp.json',
                ['signed', 'newkey'],
                'new value',
                new SignatureThresholdException('Signature threshold not met on timestamp'),
                [
                    'timestamp' => null,
                    'snapshot' => 2,
                    'targets' => 2,
                ],
            ],
            // For snapshot.json files, adding a new key or changing the existing version number
            // will result in a MetadataException indicating that the contents hash does not match
            // the hashes specified in the timestamp.json. This is because timestamp.json in the test
            // fixtures contains the optional 'hashes' metadata for the snapshot.json files, and this
            // is checked before the file signatures and the file version number. The order of checking
            // is specified in § 5.5.
            // § 5.3.11
            // § 5.5.2
            'add key to snapshot.json' => [
                'snapshot.json',
                ['signed', 'newkey'],
                'new value',
                new MetadataException("The 'snapshot' contents does not match hash 'sha256' specified in the 'timestamp' metadata."),
                [
                    'timestamp' => 4,
                    'snapshot' => null,
                    'targets' => 2,
                ],
            ],
            // § 5.3.11
            // § 5.5.2
            'change version in snapshot.json' => [
                'snapshot.json',
                ['signed', 'version'],
                6,
                new MetadataException("The 'snapshot' contents does not match hash 'sha256' specified in the 'timestamp' metadata."),
                [
                    'timestamp' => 4,
                    'snapshot' => null,
                    'targets' => 2,
                ],
            ],
            // For targets.json files, adding a new key or changing the existing version number
            // will result in a SignatureThresholdException because currently the test
            // fixtures do not contain hashes for targets.json files in snapshot.json.
            // § 5.6.3
            'add key to targets.json' => [
                'targets.json',
                ['signed', 'newvalue'],
                'value',
                new SignatureThresholdException("Signature threshold not met on targets"),
                [
                    'timestamp' => 4,
                    'snapshot' => 4,
                    'targets' => 2,
                ],
            ],
            // § 5.6.3
            'change version in targets.json' => [
                'targets.json',
                ['signed', 'version'],
                6,
                new SignatureThresholdException("Signature threshold not met on targets"),
                [
                    'timestamp' => 4,
                    'snapshot' => 4,
                    'targets' => 2,
                ],
            ],
        ];
    }

    /**
     * Tests that if a file is missing from the repo an exception is thrown.
     *
     * @param string $fixtureName
     *   The fixtures set to use.
     * @param string $fileName
     *   The name of the file to remove from the repo.
     * @param array $expectedUpdatedVersions
     *   The expected updated versions.
     *
     * @dataProvider providerFileNotFoundExceptions
     *
     * @testdox Deleting $fileName from $fixtureName raises an exception
     */
    public function testFileNotFoundExceptions(string $fixtureName, string $fileName, array $expectedUpdatedVersions): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        // Depending on which file is removed from the server, the update
        // process will error out at various points. That's fine, because we're
        // not trying to complete the refresh.
        unset($this->serverFiles[$fileName]);
        try {
            $this->getUpdater()->refresh();
            $this->fail('No RepoFileNotFound exception thrown');
        } catch (RepoFileNotFound $exception) {
            // We don't have to do anything with this exception; we just wanted
            // be sure it got thrown. Since the exception is thrown by TestRepo,
            // there's no point in asserting that its message is as expected.
        }
        $this->assertMetadataVersions($expectedUpdatedVersions, $this->clientStorage);
    }

    /**
     * Data provider for testFileNotFoundExceptions().
     *
     * @return mixed[]
     *   The test cases for testFileNotFoundExceptions().
     */
    public function providerFileNotFoundExceptions(): array
    {
        return [
            // § 5.3.11
            'timestamp.json in Delegated' => [
                'Delegated',
                'timestamp.json',
                [
                    'timestamp' => null,
                    'snapshot' => null,
                    'targets' => 4,
                ],
            ],
            // § 5.3.11
            'snapshot.json in Delegated' => [
                'Delegated',
                'snapshot.json',
                [
                    'timestamp' => 4,
                    'snapshot' => null,
                    'targets' => 4,
                ],
            ],
            'targets.json in Delegated' => [
                'Delegated',
                'targets.json',
                [
                    'timestamp' => 4,
                    'snapshot' => 4,
                    'targets' => 2,
                ],
            ],
            'timestamp.json in Simple' => [
                'Simple',
                // Deleting timestamp.json and 1.snapshot.json from the server will cause Updater::updateTimestamp()
                // and Updater::refresh() to error out. That's fine in these cases, because we're not trying to finish
                // the refresh. This will implicitly check that Updater::updateRoot() doesn't erroneously think that
                // keys have been rotated, and therefore delete the local timestamp.json and snapshot.json.
                // @see ::testKeyRotation()
                'timestamp.json',
                [
                    'root' => 1,
                    'timestamp' => 1,
                    'snapshot' => 1,
                    'targets' => 1,
                ],
            ],
        ];
    }

    /**
     * Tests fixtures with signature thresholds greater than 1.
     *
     * @param boolean $attack
     *   Whether or not to re-use a signature in timestamp.json, simulating
     *   an attack.
     *
     * @testWith [false]
     *   [true]
     */
    public function testSignatureThresholds(bool $attack): void
    {
        // Begin with ThresholdTwo, and modify it to suit our needs.
        $this->loadClientAndServerFilesFromFixture('ThresholdTwo');

        // § 5.4.2
        // If we're simulating an attack, change the server's timestamp.json so
        // that one of its signatures is invalid and we will not be able to
        // reach the required threshold of 2.
        if ($attack) {
            $data = static::decodeJson($this->serverFiles['timestamp.json']);
            $this->assertCount(2, $data['signatures']);
            $data['signatures'][1]['sig'] = hash('sha512', 'This is just a random string.');
            $this->serverFiles['timestamp.json'] = static::encodeJson($data);

            $this->expectException(SignatureThresholdException::class);
        }
        $this->getUpdater()->refresh();
    }

    public function providerKeyRotation(): array
    {
        return [
            'no keys rotated' => [
                'PublishedTwice',
                [
                    'timestamp' => 1,
                    'snapshot' => 1,
                    'targets' => 1,
                ],
            ],
            // We expect the timestamp and snapshot metadata to be deleted from the client if either the
            // timestamp or snapshot roles' keys have been rotated.
            'timestamp rotated' => [
                'PublishedTwiceWithRotatedKeys_timestamp',
                [
                    'root' => 2,
                    'timestamp' => null,
                    'snapshot' => null,
                    'targets' => 1,
                ],
            ],
            'snapshot rotated' => [
                'PublishedTwiceWithRotatedKeys_snapshot',
                [
                    'root' => 2,
                    'timestamp' => null,
                    'snapshot' => null,
                    'targets' => 1,
                ],
            ],
        ];
    }

    /**
     * Tests that the updater correctly handles key rotation (§ 5.3.11)
     *
     * @param string $fixtureName
     *   The name of the fixture to test with.
     * @param array $expectedUpdatedVersions
     *   The expected client-side versions of the TUF metadata after refresh.
     *
     * @dataProvider providerKeyRotation
     *
     * @covers ::hasRotatedKeys
     * @covers ::updateRoot
     */
    public function testKeyRotation(string $fixtureName, array $expectedUpdatedVersions): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        // This will purposefully cause the refresh to fail, immediately after
        // updating the root metadata.
        unset($this->serverFiles['timestamp.json']);
        try {
            $this->getUpdater()->refresh();
            $this->fail('Expected a RepoFileNotFound exception, but none was thrown.');
        } catch (RepoFileNotFound) {
            // We don't need to do anything with this exception.
        }
        $this->assertMetadataVersions($expectedUpdatedVersions, $this->clientStorage);
    }

    public function providerTimestampAndSnapshotLength(): array
    {
        return [
            'unknown snapshot length' => [
                'TargetsLengthNoSnapshotLength',
                'snapshot.json',
                Repository::$maxBytes,
            ],
            'unknown targets length' => [
                'Simple',
                'targets.json',
                Repository::$maxBytes,
            ],
            'known snapshot length' => [
                'Simple',
                'snapshot.json',
                683,
            ],
            'known targets length' => [
                'TargetsLengthNoSnapshotLength',
                'targets.json',
                441,
            ],
        ];
    }

    /**
     * @dataProvider providerTimestampAndSnapshotLength
     */
    public function testTimestampAndSnapshotLength(string $fixtureName, string $downloadedFileName, int $expectedLength): void
    {
        $this->loadClientAndServerFilesFromFixture($fixtureName);
        // Remove all client-side data except for the root metadata, so that we
        // can ensure it's all refereshed from the server.
        foreach (['timestamp', 'snapshot', 'targets'] as $name) {
            $this->clientStorage->delete($name);
        }

        $this->getUpdater()->refresh();

        // The length of the timestamp metadata is never known in advance, so it
        // is always downloaded with the maximum length.
        $this->assertSame(Repository::$maxBytes, $this->serverFiles->maxBytes['timestamp.json'][0]);
        $this->assertSame($expectedLength, $this->serverFiles->maxBytes[$downloadedFileName][0]);
    }

    /**
     * @testdox Exception if $fileToChange is bigger than known size
     *
     * @testWith ["snapshot.json"]
     *   ["targets.json"]
     */
    public function testMetadataFileTooBig(string $fileToChange): void
    {
        $this->loadClientAndServerFilesFromFixture('PublishedTwice');

        // Exactly which server-side files we'll need to modify, depends on
        // whether we're using consistent snapshots.
        $consistentSnapshots = $this->serverMetadata->getRoot(1)
            ->trust()
            ->supportsConsistentSnapshots();
        // Get the known lengths of snapshot.json and targets.json.
        $snapshotInfo = $this->serverMetadata->getTimestamp()
            ->trust()
            ->getFileMetaInfo('snapshot.json');
        $targetsInfo = $this->serverMetadata->getSnapshot($consistentSnapshots ? $snapshotInfo['version'] : null)
            ->trust()
            ->getFileMetaInfo('targets.json');

        $knownLength = match ($fileToChange) {
            'snapshot.json' => $snapshotInfo['length'],
            'targets.json' => $targetsInfo['length'],
        };
        // If using consistent snapshots, the file to change will be prefixed
        // with its version number.
        if ($consistentSnapshots) {
            $prefix = match ($fileToChange) {
                'snapshot.json' => $snapshotInfo['version'],
                'targets.json' => $targetsInfo['version'],
            };
            $fileToChange = "$prefix.$fileToChange";
        }
        // On the server, replace $fileToChange with a string that's longer than
        // the known length, which should cause an exception during the update.
        $this->serverFiles[$fileToChange] = str_repeat('a', $knownLength + 1);

        $this->expectException(DownloadSizeException::class);
        $this->expectExceptionMessage("Expected $fileToChange to be $knownLength bytes.");
        $this->getUpdater()->refresh();
    }
}
