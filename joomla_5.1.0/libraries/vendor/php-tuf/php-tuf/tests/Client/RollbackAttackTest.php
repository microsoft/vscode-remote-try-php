<?php

namespace Tuf\Tests\Client;

use Tuf\Exception\Attack\RollbackAttackException;
use Tuf\Tests\ClientTestBase;
use Tuf\Tests\TestHelpers\DurableStorage\TestStorage;

/**
 * Tests that server-side rollback attacks are detected.
 */
class RollbackAttackTest extends ClientTestBase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Always throw an exception if writing to client storage.
        $this->clientStorage = new class () extends TestStorage {

            /**
             * {@inheritDoc}
             */
            public function write(string $name, string $data): void
            {
                throw new \LogicException("Unexpected attempt to change client storage.");
            }

            /**
             * {@inheritDoc}
             */
            public function delete(string $name): void
            {
                throw new \LogicException("Unexpected attempt to change client storage.");
            }

        };
    }

    /**
     * @testWith ["consistent"]
     *   ["inconsistent"]
     */
    public function testRollbackAttackDetection(string $fixtureVariant): void
    {
        $this->loadClientAndServerFilesFromFixture("AttackRollback/$fixtureVariant");
        try {
            // § 5.4.3
            // § 5.4.4
            $this->getUpdater()->refresh();
            $this->fail('No exception thrown.');
        } catch (RollbackAttackException $exception) {
            $this->assertSame('Remote timestamp metadata version "$1" is less than previously seen timestamp version "$2"', $exception->getMessage());
            $this->assertMetadataVersions([
                'root' => 2,
                'timestamp' => 2,
                'snapshot' => 2,
                'targets' => 2,
            ], $this->clientStorage);
        }
    }
}
