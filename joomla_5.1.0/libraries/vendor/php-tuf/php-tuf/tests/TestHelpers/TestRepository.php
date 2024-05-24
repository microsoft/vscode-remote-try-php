<?php

namespace Tuf\Tests\TestHelpers;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Tuf\Client\Repository;

/**
 * Allows mocked metadata objects to be returned from the server in tests.
 */
class TestRepository extends Repository
{
    /**
     * The mocked targets metadata, keyed by role name and version number.
     *
     * @var \Tuf\Metadata\TargetsMetadata[][]
     *
     * @see ::getTargets()
     */
    public array $targets = [];

    /**
     * {@inheritDoc}
     */
    public function getTargets(?int $version, string $role = 'targets', int $maxBytes = null): PromiseInterface
    {
        if (!empty($this->targets[$role])) {
            $version ??= array_key_last($this->targets[$role]);
            return Create::promiseFor($this->targets[$role][$version]);
        }
        return parent::getTargets($version, $role, $maxBytes);
    }
}
