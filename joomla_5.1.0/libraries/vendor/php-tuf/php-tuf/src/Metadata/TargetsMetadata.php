<?php

namespace Tuf\Metadata;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Tuf\DelegatedRole;
use Tuf\Exception\NotFoundException;
use Tuf\Key;

class TargetsMetadata extends MetadataBase
{

    /**
     * {@inheritdoc}
     */
    public const TYPE = 'targets';

    /**
     * The role name if different from the type.
     *
     * @var string
     */
    private ?string $role;

    /**
     * {@inheritdoc}
     *
     * @param string|null $roleName
     *   The role name if not the same as the type.
     */
    public static function createFromJson(string $json, string $roleName = null): static
    {
        $newMetadata = parent::createFromJson($json);
        $newMetadata->role = $roleName;
        return $newMetadata;
    }

    /**
     * Validates that delegated role names are unique.
     *
     * @todo Use Symfony's Unique constraint for this when at least Symfony
     *   6.1 is required in https://github.com/php-tuf/php-tuf/issues/317.
     *
     * @param mixed $delegations
     *   The value to be validated.
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     *   The validation context.
     */
    public static function validateDelegatedRoles(mixed $delegations, ExecutionContextInterface $context): void
    {
        if (!is_array($delegations)) {
            return;
        }
        $names = array_column($delegations['roles'] ?? [], 'name');
        if ($names !== array_unique($names)) {
            $context->addViolation('Delegated role names must be unique.');
        }
    }

    /**
     * Returns a canonical JSON representation of this metadata object.
     *
     * @return string
     *   The canonical JSON representation of this object.
     */
    public function toCanonicalJson(): string
    {
        $metadata = $this->getSigned();

        // Apply sorting
        self::sortKeys($metadata);

        foreach ($metadata['targets'] as $path => $target) {
            // Custom target info should always encode to an object, even if
            // it's empty.
            if (array_key_exists('custom', $target)) {
                $metadata['targets'][$path]['custom'] = (object) $target['custom'];
            }
        }

        // Ensure that these will encode as objects even if they're empty.
        $metadata['targets'] = (object) $metadata['targets'];
        if (array_key_exists('delegations', $metadata)) {
            $metadata['delegations']['keys'] = (object) $metadata['delegations']['keys'];
        }

        return static::encodeJson($metadata);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getSignedCollectionOptions(): array
    {
        $options = parent::getSignedCollectionOptions();
        $options['fields']['delegations'] = new Optional([
            new Collection([
                'keys' => new Required([
                    new Type('array'),
                    new All([
                        static::getKeyConstraints(),
                    ]),
                ]),
                'roles' => new All([
                    new Type('array'),
                    new Collection([
                        'fields' => [
                            'name' => [
                                new NotBlank(),
                                new Type('string'),
                            ],
                            'paths' => new Optional([
                                new Type('array'),
                                new All([
                                    new Type('string'),
                                    new NotBlank(),
                                ]),
                            ]),
                            'path_hash_prefixes' => new Optional([
                                new Type('array'),
                                new All([
                                    new Type('string'),
                                    new NotBlank(),
                                ]),
                            ]),
                            'terminating' => [
                                new Type('boolean'),
                            ],
                        ] + static::getKeyidsConstraints() + static::getThresholdConstraints(),
                    ]),
                ]),
            ]),
            new Callback([static::class, 'validateDelegatedRoles']),
        ]);
        $options['fields']['targets'] = new Required([
            new All([
                new Collection([
                    'length' => [
                        new Type('integer'),
                        new GreaterThanOrEqual(1),
                    ],
                    'custom' => new Optional([
                        new Type('array'),
                    ]),
                ] + static::getHashesConstraints()),
            ]),

        ]);
        return $options;
    }

    /**
     * Returns the length, in bytes, of a specific target.
     *
     * @param string $target
     *   The target path.
     *
     * @return integer
     *   The length (size) of the target, in bytes.
     */
    public function getLength(string $target): int
    {
        return $this->getInfo($target)['length'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRole(): string
    {
        return $this->role ?? $this->getType();
    }

    /**
     * Returns the known hashes for a specific target.
     *
     * @param string $target
     *   The target path.
     *
     * @return array
     *   The known hashes for the object. The keys are the hash algorithm (e.g.
     *   'sha256') and the values are the hash digest.
     */
    public function getHashes(string $target): array
    {
        return $this->getInfo($target)['hashes'];
    }

    /**
     * Determines if a target is specified in the current metadata.
     *
     * @param string $target
     *   The target path.
     *
     * @return bool
     *   True if the target is specified, or false otherwise.
     */
    public function hasTarget(string $target): bool
    {
        try {
            $this->getInfo($target);
            return true;
        } catch (NotFoundException $exception) {
            return false;
        }
    }

    /**
     * Gets info about a specific target.
     *
     * @param string $target
     *   The target path.
     *
     * @return array
     *   The target's info.
     *
     * @throws \Tuf\Exception\NotFoundException
     *   Thrown if the target is not mentioned in this metadata.
     */
    protected function getInfo(string $target): array
    {
        $signed = $this->getSigned();
        if (isset($signed['targets'][$target])) {
            return $signed['targets'][$target];
        }
        throw new NotFoundException($target, 'Target');
    }

    /**
     * Gets the delegated keys if any.
     *
     * @return \Tuf\Key[]
     *   The delegated keys.
     */
    public function getDelegatedKeys(): array
    {
        $keys = [];
        foreach ($this->getSigned()['delegations']['keys'] ?? [] as $keyId => $keyInfo) {
            $keys[$keyId] = Key::createFromMetadata($keyInfo);
        }
        return $keys;
    }

    /**
     * Gets the delegated roles if any.
     *
     * @return \Tuf\DelegatedRole[]
     *   The delegated roles.
     */
    public function getDelegatedRoles(): array
    {
        $roles = [];
        foreach ($this->getSigned()['delegations']['roles'] ?? [] as $roleInfo) {
            $role = DelegatedRole::createFromMetadata($roleInfo);
            $roles[$role->getName()] = $role;
        }
        return $roles;
    }
}
