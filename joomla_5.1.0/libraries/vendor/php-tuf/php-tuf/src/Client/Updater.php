<?php

namespace Tuf\Client;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\StreamInterface;
use Tuf\Exception\MetadataException;
use Tuf\Exception\NotFoundException;
use Tuf\Exception\Attack\DenialOfServiceAttackException;
use Tuf\Exception\Attack\InvalidHashException;
use Tuf\Helper\Clock;
use Tuf\Loader\SizeCheckingLoader;
use Tuf\Metadata\RootMetadata;
use Tuf\Metadata\SnapshotMetadata;
use Tuf\Metadata\StorageInterface;
use Tuf\Metadata\TargetsMetadata;
use Tuf\Metadata\TimestampMetadata;
use Tuf\Metadata\Verifier\UniversalVerifier;
use Tuf\Metadata\Verifier\RootVerifier;

/**
 * Class Updater
 *
 * @package Tuf\Client
 */
class Updater
{

    const MAX_ROOT_DOWNLOADS = 1024;

    /**
     * The maximum number of target roles supported.
     *
     * § 5.6.7.1
     */
    const MAXIMUM_TARGET_ROLES = 100;

    /**
     * Whether the repo has been refreshed or not.
     *
     * @see ::download()
     * @see ::refresh()
     *
     * @var bool
     */
    protected bool $isRefreshed = false;

    /**
     * @var \Tuf\Client\SignatureVerifier
     */
    protected SignatureVerifier $signatureVerifier;

    /**
     * @var \Tuf\Helper\Clock
     */
    protected Clock $clock;

    /**
     * The time after which metadata should be considered expired.
     *
     * @var \DateTimeImmutable
     */
    private ?\DateTimeImmutable $metadataExpiration;

    /**
     * The verifier factory.
     *
     * @var \Tuf\Metadata\Verifier\UniversalVerifier
     */
    protected UniversalVerifier $universalVerifier;

    /**
     * The backend to load untrusted metadata from the server.
     *
     * @var \Tuf\Client\Repository
     */
    protected Repository $server;

    /**
     * Updater constructor.
     *
     * @param \Tuf\Loader\SizeCheckingLoader $serverLoader
     *   The backend to load data from the server.
     *  @param \Tuf\Metadata\StorageInterface $storage
     *     The storage backend for trusted metadata. Should be available to
     *     future instances of Updater that interact with the same repository.
     *
     *@todo What is this for?
     *       https://github.com/php-tuf/php-tuf/issues/161
     */
    public function __construct(private SizeCheckingLoader $serverLoader, protected StorageInterface $storage)
    {
        $this->server = new Repository($this->serverLoader);
        $this->clock = new Clock();
    }

    /**
     * @todo Add docs. See python comments:
     *     https://github.com/theupdateframework/tuf/blob/1cf085a360aaad739e1cc62fa19a2ece270bb693/tuf/client/updater.py#L999
     *     https://github.com/php-tuf/php-tuf/issues/162
     * @todo The Python implementation has an optional flag to "unsafely update
     *     root if necessary". Do we need it?
     *     https://github.com/php-tuf/php-tuf/issues/21
     *
     * @param bool $force
     *   (optional) If false, return early if this updater has already been
     *   refreshed. Defaults to false.
     *
     * @return boolean
     *     TRUE if the data was successfully refreshed.
     *
     * @see https://github.com/php-tuf/php-tuf/issues/21
     *
     * @throws \Tuf\Exception\MetadataException
     *   Throw if an upated root metadata file is not valid.
     * @throws \Tuf\Exception\Attack\FreezeAttackException
     *   Throw if a freeze attack is detected.
     * @throws \Tuf\Exception\Attack\RollbackAttackException
     *   Throw if a rollback attack is detected.
     * @throws \Tuf\Exception\Attack\SignatureThresholdException
     *   Thrown if the signature threshold has not be reached.
     */
    public function refresh(bool $force = false): bool
    {
        if ($force) {
            $this->isRefreshed = false;
            $this->metadataExpiration = null;
        }
        if ($this->isRefreshed) {
            return true;
        }

        // § 5.1
        $this->metadataExpiration = $this->getUpdateStartTime();

        // § 5.2
        /** @var \Tuf\Metadata\RootMetadata $rootData */
        $rootData = $this->storage->getRoot();

        $this->signatureVerifier = SignatureVerifier::createFromRootMetadata($rootData);
        $this->universalVerifier = new UniversalVerifier($this->storage, $this->signatureVerifier, $this->metadataExpiration);

        // § 5.3
        $this->updateRoot($rootData);

        // § 5.4
        $currentTimestamp = $this->storage->getTimestamp();
        $newTimestampData = $this->updateTimestamp();
        // § 5.4.3.1
        if ($currentTimestamp && $currentTimestamp->getVersion() === $newTimestampData->getVersion()) {
            $this->storage->save($currentTimestamp);
            $this->isRefreshed = true;
            return true;
        }

        $snapshotInfo = $newTimestampData->getFileMetaInfo('snapshot.json');

        // § 5.5
        $snapshotVersion = $rootData->supportsConsistentSnapshots()
            ? $snapshotInfo['version']
            : null;
        // § 5.5.1
        $newSnapshotData = $this->server->getSnapshot($snapshotVersion, $snapshotInfo['length'] ?? null);
        $this->universalVerifier->verify(SnapshotMetadata::TYPE, $newSnapshotData);
        // § 5.5.7
        $this->storage->save($newSnapshotData);

        // § 5.6
        $this->fetchAndVerifyTargetsMetadata('targets')->wait();

        $this->isRefreshed = true;
        return true;
    }

    /**
     * Updates the timestamp role, per section 5.3 of the TUF spec.
     */
    private function updateTimestamp(): TimestampMetadata
    {
        // § 5.4.1
        $newTimestampData = $this->server->getTimestamp();

        $this->universalVerifier->verify(TimestampMetadata::TYPE, $newTimestampData);

        // § 5.4.5: Persist timestamp metadata
        $this->storage->save($newTimestampData);

        return $newTimestampData;
    }



    /**
     * Updates the root metadata if needed.
     *
     * @param \Tuf\Metadata\RootMetadata $rootData
     *   The current root metadata.
     *
     * @return void
     *@throws \Tuf\Exception\Attack\FreezeAttackException
     *   Throw if a freeze attack is detected.
     * @throws \Tuf\Exception\Attack\RollbackAttackException
     *   Throw if a rollback attack is detected.
     * @throws \Tuf\Exception\Attack\SignatureThresholdException
     *   Thrown if an updated root file is not signed with the need signatures.
     *
     * @throws \Tuf\Exception\MetadataException
     *   Throw if an upated root metadata file is not valid.
     */
    private function updateRoot(RootMetadata &$rootData): void
    {
        // § 5.3.1 needs no action, since we currently require consistent
        // snapshots.
        $rootsDownloaded = 0;
        $originalRootData = $rootData;
        // § 5.3.2 and 5.3.3
        $nextVersion = $rootData->getVersion() + 1;

        while ($nextRoot = $this->server->getRoot($nextVersion)) {
            $rootsDownloaded++;
            if ($rootsDownloaded > static::MAX_ROOT_DOWNLOADS) {
                throw new DenialOfServiceAttackException("The maximum number root files have already been downloaded: " . static::MAX_ROOT_DOWNLOADS);
            }
            $this->universalVerifier->verify(RootMetadata::TYPE, $nextRoot);

            // § 5.3.6 Needs no action. The expiration of the new (intermediate)
            // root metadata file does not matter yet, because we will check for
            // it in § 5.3.10.
            // § 5.3.7
            $rootData = $nextRoot;

            // Recreate Verifier with new root metadata
            $this->signatureVerifier = SignatureVerifier::createFromRootMetadata($rootData);
            $this->universalVerifier = new UniversalVerifier($this->storage, $this->signatureVerifier, $this->metadataExpiration);

            // § 5.3.8
            $this->storage->save($nextRoot);
            // § 5.3.9: repeat from § 5.3.2.
            $nextVersion = $rootData->getVersion() + 1;
        }
        // § 5.3.10
        RootVerifier::checkFreezeAttack($rootData, $this->metadataExpiration);

        // § 5.3.11: Delete the trusted timestamp and snapshot files if either
        // file has rooted keys.
        if ($rootsDownloaded &&
           (static::hasRotatedKeys($originalRootData, $rootData, 'timestamp')
           || static::hasRotatedKeys($originalRootData, $rootData, 'snapshot'))) {
            $this->storage->delete(TimestampMetadata::TYPE);
            $this->storage->delete(SnapshotMetadata::TYPE);
        }
        // § 5.3.12 needs no action because we currently require consistent
        // snapshots.
    }

    /**
     * Determines if the new root metadata has rotated keys for a role.
     *
     * @param \Tuf\Metadata\RootMetadata $previousRootData
     *   The previous root metadata.
     * @param \Tuf\Metadata\RootMetadata $newRootData
     *   The new root metadta.
     * @param string $role
     *   The role to check for rotated keys.
     *
     * @return boolean
     *   True if the keys for the role have been rotated, otherwise false.
     */
    private static function hasRotatedKeys(RootMetadata $previousRootData, RootMetadata $newRootData, string $role): bool
    {
        $previousRole = $previousRootData->getRoles()[$role] ?? null;
        $newRole = $newRootData->getRoles()[$role] ?? null;
        if ($previousRole && $newRole) {
            return !$previousRole->keysMatch($newRole);
        }
        return false;
    }

    /**
     * Verifies a stream of data against a known TUF target.
     *
     * @param string $target
     *   The path of the target file. Needs to be known to the most recent
     *   targets metadata downloaded in ::refresh().
     * @param \Psr\Http\Message\StreamInterface $data
     *   A stream pointing to the downloaded target data.
     *
     * @throws \Tuf\Exception\MetadataException
     *   If the target has no trusted hash(es).
     * @throws \Tuf\Exception\Attack\InvalidHashException
     *   If the data stream does not match the known hash(es) for the target.
     */
    protected function verify(string $target, StreamInterface $data): void
    {
        $this->refresh();

        $targetsMetadata = $this->getMetadataForTarget($target);
        if ($targetsMetadata === null) {
            throw new NotFoundException($target, 'Target');
        }

        $hashes = $targetsMetadata->getHashes($target);
        if (count($hashes) === 0) {
            // § 5.7.2
            throw new MetadataException("No trusted hashes are available for '$target'");
        }
        foreach ($hashes as $algo => $hash) {
            // If the stream has a URI that refers to a file, use
            // hash_file() to verify it. Otherwise, read the entire stream
            // as a string and use hash() to verify it.
            $uri = $data->getMetadata('uri');
            if ($uri && file_exists($uri)) {
                $streamHash = hash_file($algo, $uri);
            } else {
                $streamHash = hash($algo, $data->getContents());
                $data->rewind();
            }

            if ($hash !== $streamHash) {
                throw new InvalidHashException($data, "Invalid $algo hash for $target");
            }
        }
    }

    /**
     * Downloads a target file, verifies it, and returns its contents.
     *
     * @param string $target
     *   The path of the target file. Needs to be known to the most recent
     *   targets metadata downloaded in ::refresh().
     *
     * @return \GuzzleHttp\Promise\PromiseInterface<\Psr\Http\Message\StreamInterface>
     *   A promise wrapping a stream of the trusted, downloaded data.
     */
    public function download(string $target): PromiseInterface
    {
        $this->refresh();

        // The target needs to be known to the most recent targets metadata
        // that we downloaded during ::refresh().
        $targetsMetadata = $this->getMetadataForTarget($target);
        if ($targetsMetadata === null) {
            throw new NotFoundException($target, 'Target');
        }

        return $this->serverLoader->load($target, $targetsMetadata->getLength($target) ?? Repository::$maxBytes)
          ->then(function (StreamInterface $stream) use ($target) {
             $this->verify($target, $stream);
             return $stream;
          });
    }

    /**
     * Gets a target metadata object that contains the specified target, if any.
     *
     * @param string $target
     *   The path of the target file.
     *
     * @return \Tuf\Metadata\TargetsMetadata|null
     *   The targets metadata with information about the desired target, or null if no relevant metadata is found.
     */
    protected function getMetadataForTarget(string $target): ?TargetsMetadata
    {
        // Search the top level targets metadata.
        /** @var \Tuf\Metadata\TargetsMetadata $targetsMetadata */
        $targetsMetadata = $this->storage->getTargets();
        if ($targetsMetadata->hasTarget($target)) {
            return $targetsMetadata;
        }
        // Recursively search any delegated roles.
        return $this->searchDelegatedRolesForTarget($targetsMetadata, $target, ['targets']);
    }

    /**
     * Fetches and verifies a targets metadata file.
     *
     * The metadata file will be stored as '$role.json'.
     *
     * @param string $role
     *   The role name. This may be 'targets' or a delegated role.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface<\Tuf\Metadata\TargetsMetadata>
     *   A promise wrapping the verified metadata for the role.
     */
    private function fetchAndVerifyTargetsMetadata(string $role): PromiseInterface
    {
        $fileInfo = $this->storage->getSnapshot()->getFileMetaInfo("$role.json");
        // § 5.6.1
        $targetsVersion = $this->storage->getRoot()->supportsConsistentSnapshots()
            ? $fileInfo['version']
            : null;

        return $this->server->getTargets($targetsVersion, $role, $fileInfo['length'] ?? null)
          ->then(function (TargetsMetadata $newTargetsData) {
              $this->universalVerifier->verify(TargetsMetadata::TYPE, $newTargetsData);
              // § 5.5.6
              $this->storage->save($newTargetsData);
              return $newTargetsData;
          });
    }

    /**
     * Returns the time that the update began.
     *
     * @return \DateTimeImmutable
     *   The time that the update began.
     */
    private function getUpdateStartTime(): \DateTimeImmutable
    {
        return (new \DateTimeImmutable())->setTimestamp($this->clock->getCurrentTime());
    }

    /**
     * Searches delegated roles for metadata concerning a specific target.
     *
     * @param \Tuf\Metadata\TargetsMetadata|null $targetsMetadata
     *   The targets metadata to search.
     * @param string $target
     *   The path of the target file.
     * @param string[] $searchedRoles
     *   The roles that have already been searched. This is for internal use only and should not be passed by calling code.
     * @param bool $terminated
     *   (optional) For internal recursive calls only. This will be set to true if a terminating delegation is found in
     *   the search.
     *
     *
     * @return \Tuf\Metadata\TargetsMetadata|null
     *   The target metadata that contains the metadata for the target or null if the target is not found.
     */
    private function searchDelegatedRolesForTarget(TargetsMetadata $targetsMetadata, string $target, array $searchedRoles, bool &$terminated = false): ?TargetsMetadata
    {
        foreach ($targetsMetadata->getDelegatedKeys() as $keyId => $delegatedKey) {
            $this->signatureVerifier->addKey($keyId, $delegatedKey);
        }

        $delegatedRoles = [];
        foreach ($targetsMetadata->getDelegatedRoles() as $delegatedRole) {
            // Targets must match the paths of all roles in the delegation chain, so if the path does not match,
            // do not evaluate this role or any roles it delegates to.
            if ($delegatedRole->matchesPath($target)) {
                $delegatedRoles[] = $delegatedRole;

                if ($delegatedRole->isTerminating()) {
                    break;
                }
            }
        }

        foreach ($delegatedRoles as $delegatedRole) {
            $delegatedRoleName = $delegatedRole->getName();
            if (in_array($delegatedRoleName, $searchedRoles, true)) {
                // § 5.6.7.1
                // If this role has been visited before, skip it (to avoid cycles in the delegation graph).
                continue;
            }
            // § 5.6.7.1
            if (count($searchedRoles) > static::MAXIMUM_TARGET_ROLES) {
                return null;
            }

            $this->signatureVerifier->addRole($delegatedRole);
            /** @var \Tuf\Metadata\TargetsMetadata $delegatedTargetsMetadata */
            $delegatedTargetsMetadata = $this->fetchAndVerifyTargetsMetadata($delegatedRoleName)
              ->wait();
            if ($delegatedTargetsMetadata->hasTarget($target)) {
                return $delegatedTargetsMetadata;
            }
            $searchedRoles[] = $delegatedRoleName;
            // § 5.6.7.2
            // Recursively search the list of delegations in order of appearance.
            $delegatedRolesMetadataSearchResult = $this->searchDelegatedRolesForTarget($delegatedTargetsMetadata, $target, $searchedRoles, $terminated);
            if ($terminated || $delegatedRolesMetadataSearchResult) {
                return $delegatedRolesMetadataSearchResult;
            }

            // If $delegatedRole is terminating then we do not search any of the next delegated roles after it
            // in the delegations from $targetsMetadata.
            if ($delegatedRole->isTerminating()) {
                $terminated = true;
                // § 5.6.7.2.1
                // If the role is terminating then abort searching for a target.
                return null;
            }
        }
        return null;
    }
}
