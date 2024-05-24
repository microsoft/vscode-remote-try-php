<?php

namespace Tuf\Metadata\Verifier;

use Tuf\Client\SignatureVerifier;
use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\RootMetadata;
use Tuf\Metadata\SnapshotMetadata;
use Tuf\Metadata\StorageInterface;
use Tuf\Metadata\TimestampMetadata;

/**
 * Verifies untrusted metadata.
 */
class UniversalVerifier
{
    /**
     * Factory constructor.
     *
     * @param \Tuf\Metadata\StorageInterface $storage
     *   The durable metadata storage.
     * @param \Tuf\Client\SignatureVerifier $signatureVerifier
     *   The signature verifier.
     * @param \DateTimeImmutable $metadataExpiration
     *   The time beyond which untrusted metadata will be considered expired.
     */
    public function __construct(private StorageInterface $storage, private SignatureVerifier $signatureVerifier, private \DateTimeImmutable $metadataExpiration)
    {
    }

    /**
     * Verifies an untrusted metadata object for a role.
     *
     * @param string $role
     *   The metadata role (e.g. 'root', 'targets', etc.)
     * @param \Tuf\Metadata\MetadataBase $untrustedMetadata
     *   The untrusted metadata object.
     *
     * @throws \Tuf\Exception\Attack\FreezeAttackException
     * @throws \Tuf\Exception\Attack\RollbackAttackException
     * @throws \Tuf\Exception\Attack\InvalidHashException
     * @throws \Tuf\Exception\Attack\SignatureThresholdException
     */
    public function verify(string $role, MetadataBase $untrustedMetadata): void
    {
        $verifier = match ($role) {
            RootMetadata::TYPE =>
                new RootVerifier($this->signatureVerifier, $this->metadataExpiration, $this->storage->getRoot()),

            SnapshotMetadata::TYPE =>
                new SnapshotVerifier($this->signatureVerifier, $this->metadataExpiration, $this->storage->getSnapshot(), $this->storage->getTimestamp()),

            TimestampMetadata::TYPE =>
                new TimestampVerifier($this->signatureVerifier, $this->metadataExpiration, $this->storage->getTimestamp()),

            default =>
                new TargetsVerifier($this->signatureVerifier, $this->metadataExpiration, $this->storage->getTargets($role), $this->storage->getSnapshot()),
        };
        $verifier->verify($untrustedMetadata);
        // If the verifier didn't throw an exception, we can trust this metadata.
        $untrustedMetadata->trust();
    }
}
