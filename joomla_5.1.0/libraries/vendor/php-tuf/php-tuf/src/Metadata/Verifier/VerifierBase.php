<?php

namespace Tuf\Metadata\Verifier;

use Tuf\Client\SignatureVerifier;
use Tuf\Exception\Attack\FreezeAttackException;
use Tuf\Exception\Attack\RollbackAttackException;
use Tuf\Metadata\MetadataBase;

/**
 * A base class for metadata verifiers.
 */
abstract class VerifierBase
{
    /**
     * VerifierBase constructor.
     *
     * @param \Tuf\Client\SignatureVerifier $signatureVerifier
     *   The signature verifier.
     * @param \DateTimeImmutable $metadataExpiration
     *   The time beyond which untrusted metadata is considered expired.
     * @param \Tuf\Metadata\MetadataBase|null $trustedMetadata
     *   The trusted metadata, if any.
     */
    public function __construct(protected SignatureVerifier $signatureVerifier, protected \DateTimeImmutable $metadataExpiration, protected ?MetadataBase $trustedMetadata = null)
    {
        if ($trustedMetadata) {
            $trustedMetadata->ensureIsTrusted();
        }
    }

    /**
     * Verify metadata according to the specification.
     *
     * @param \Tuf\Metadata\MetadataBase $untrustedMetadata
     *   The untrusted metadata to verify.
     *
     * @throws \Tuf\Exception\Attack\FreezeAttackException
     * @throws \Tuf\Exception\Attack\RollbackAttackException
     * @throws \Tuf\Exception\Attack\InvalidHashException
     * @throws \Tuf\Exception\Attack\SignatureThresholdException
     */
    abstract public function verify(MetadataBase $untrustedMetadata): void;

    /**
     * Checks for a rollback attack.
     *
     * Verifies that an incoming remote version of a metadata file is greater
     * than or equal to the last known version.
     *
     * @param \Tuf\Metadata\MetadataBase $untrustedMetadata
     *     The untrusted metadata.
     *
     * @return void
     *
     * @throws \Tuf\Exception\Attack\RollbackAttackException
     *     Thrown if a potential rollback attack is detected.
     */
    protected function checkRollbackAttack(MetadataBase $untrustedMetadata): void
    {
        $type = $this->trustedMetadata->getType();
        $remoteVersion = $untrustedMetadata->getVersion();
        $localVersion = $this->trustedMetadata->getVersion();
        if ($remoteVersion < $localVersion) {
            $message = "Remote $type metadata version \"$$remoteVersion\" " .
              "is less than previously seen $type version \"$$localVersion\"";
            throw new RollbackAttackException($message);
        }
    }

    /**
     * Checks for a freeze attack.
     *
     * Verifies that metadata has not expired, and assumes a potential freeze
     * attack if it has.
     *
     * @param \Tuf\Metadata\MetadataBase $metadata
     *     The metadata to check.
     * @param \DateTimeImmutable $expiration
     *     The metadata expiration.
     *
     * @throws \Tuf\Exception\Attack\FreezeAttackException
     *   Thrown if a potential freeze attack is detected.
     */
    protected static function checkFreezeAttack(MetadataBase $metadata, \DateTimeImmutable $expiration): void
    {
        $metadataExpiration = $metadata->getExpires();
        if ($metadataExpiration < $expiration) {
            $format = "Remote %s metadata expired on %s";
            throw new FreezeAttackException(sprintf($format, $metadata->getRole(), $metadataExpiration->format('c')));
        }
    }
}
