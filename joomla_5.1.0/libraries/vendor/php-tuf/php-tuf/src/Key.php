<?php

namespace Tuf;

use Tuf\Metadata\ConstraintsTrait;

/**
 * Class that represents key metadata.
 */
final class Key
{
    use CanonicalJsonTrait;
    use ConstraintsTrait;

    /**
     * Key constructor.
     *
     * @param string $type
     *   The key type.
     * @param string $scheme
     *   The key scheme.
     * @param string $public
     *   The public key value.
     */
    private function __construct(private string $type, private string $scheme, private string $public)
    {
    }

    /**
     * Creates a key object from TUF metadata.
     *
     * @param array $keyInfo
     *   The key information from TUF metadata, including:
     *   - keytype: The public key signature system, e.g. 'ed25519'.
     *   - scheme: The corresponding signature scheme, e.g. 'ed25519'.
     *   - keyval: An associative array containing the public key value.

     *
     * @return static
     *
     * @see https://theupdateframework.github.io/specification/v1.0.32#document-formats
     */
    public static function createFromMetadata(array $keyInfo): self
    {
        self::validate($keyInfo, static::getKeyConstraints());
        return new static(
            $keyInfo['keytype'],
            $keyInfo['scheme'],
            $keyInfo['keyval']['public']
        );
    }

    /**
     * Gets the public key value.
     *
     * @return string
     *   The public key value.
     */
    public function getPublic(): string
    {
        return $this->public;
    }

    /**
     * Gets the key type.
     *
     * @return string
     *   The key type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Computes the key ID.
     *
     * Per specification section 4.2, the KEYID is a hexdigest of the SHA-256
     * hash of the canonical form of the key.
     *
     * @return string
     *     The key ID in hex format for the key metadata hashed using sha256.
     *
     * @see https://theupdateframework.github.io/specification/v1.0.32#document-formats
     *
     * @todo https://github.com/php-tuf/php-tuf/issues/56
     */
    public function getComputedKeyId(): string
    {
        // @see https://github.com/secure-systems-lab/securesystemslib/blob/master/securesystemslib/keys.py
        // The keyid_hash_algorithms array value is based on the TUF settings,
        // it's not expected to be part of the key metadata. The fact that it is
        // currently included is a quirk of the TUF python code that may be
        // fixed in future versions. Calculate using the normal TUF settings
        // since this is how it's calculated in the securesystemslib code and
        // any value for keyid_hash_algorithms in the key data in root.json is
        // ignored.
        $canonical = self::encodeJson([
            'keytype' => $this->getType(),
            'scheme' => $this->scheme,
            'keyid_hash_algorithms' => ['sha256', 'sha512'],
            'keyval' => [
                'public' => $this->getPublic(),
            ],
        ]);
        return hash('sha256', $canonical);
    }
}
