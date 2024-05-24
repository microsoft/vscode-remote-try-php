<?php

namespace Tuf\Tests\TestHelpers;

use PHPUnit\Framework\Assert;
use Tuf\Metadata\StorageInterface;
use Tuf\Tests\TestHelpers\DurableStorage\TestStorage;

/**
 * Contains methods for safely interacting with the test fixtures.
 */
trait FixturesTrait
{
    /**
     * Returns the initial client-side metadata versions for a fixture.
     *
     * @param string $fixtureName
     *     The name of the fixture to use.
     *
     * @return array
     *   The expected versions of the initial client-side metadata, keyed by
     *   role.
     */
    protected static function getClientStartVersions(string $fixtureName): array
    {
        $path = static::getFixturePath($fixtureName, 'client_versions.ini', false);
        return parse_ini_file($path, false, INI_SCANNER_TYPED);
    }

    /**
     * Uses test fixtures at a given path to populate a memory storage backend.
     *
     * @param string $fixtureName
     *     The name of the fixture to use.
     * @param string $path
     *     An optional relative sub-path within the fixture's directory.
     *     Defaults to the directory containing client metadata.
     *
     * @return TestStorage
     *     Memory storage containing the test data.
     */
    protected static function loadFixtureIntoMemory(string $fixtureName, string $path = 'client/metadata/current'): TestStorage
    {
        $path = static::getFixturePath($fixtureName, $path, true);
        return TestStorage::createFromDirectory($path);
    }

    /**
     * Gets the real path of repository fixtures.
     *
     * @param string $fixtureName
     *   The fixtures set to use.
     * @param string $subPath
     *   The path.
     * @param boolean $isDir
     *   Whether $path is expected to be a directory.
     *
     * @return string
     *   The path.
     */
    protected static function getFixturePath(string $fixtureName, string $subPath = '', bool $isDir = true): string
    {
        $realpath = realpath(__DIR__ . "/../../fixtures/$fixtureName/$subPath");
        Assert::assertNotEmpty($realpath);

        if ($isDir) {
            Assert::assertDirectoryExists($realpath);
        } else {
            Assert::assertFileExists($realpath);
        }
        return $realpath;
    }

    /**
     * Asserts that stored metadata are at expected versions.
     *
     * @param ?int[] $expectedVersions
     *   The expected versions. The keys are the file names, without the .json
     *   suffix, and the values are the expected version numbers, or NULL if
     *   the file should not be present.
     * @param \Tuf\Metadata\StorageInterface $storage
     *   The durable storage for the metadata.
     */
    protected static function assertMetadataVersions(array $expectedVersions, StorageInterface $storage): void
    {
        foreach ($expectedVersions as $role => $version) {
            $metadata = match ($role) {
                'root' => $storage->getRoot(),
                'timestamp' => $storage->getTimestamp(),
                'snapshot' => $storage->getSnapshot(),
                default => $storage->getTargets($role),
            };

            if (is_null($version)) {
                Assert::assertNull($metadata, "'$role' file is null.");
                return;
            }
            Assert::assertNotNull($metadata, "'$role.json' not found in local repo.");

            $actualVersion = $metadata->getVersion();
            Assert::assertSame(
                $version,
                $actualVersion,
                "Actual version of $role, '$actualVersion' does not match expected version '$version'"
            );
        }
    }
}
