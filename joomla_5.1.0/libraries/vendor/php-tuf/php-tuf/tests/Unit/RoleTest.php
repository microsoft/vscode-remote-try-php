<?php

namespace Tuf\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tuf\Exception\MetadataException;
use Tuf\Role;

/**
 * @coversDefaultClass \Tuf\Role
 */
class RoleTest extends TestCase
{

    /**
     * The test role.
     *
     * @var \Tuf\Role
     */
    protected $role;

    /**
     * @covers ::createFromMetadata
     * @covers ::getName
     * @covers ::getThreshold
     */
    public function testCreateFromMetadata(): void
    {
        $this->role = $this->createTestRole();
        self::assertSame(1000, $this->role->getThreshold());
        self::assertSame('my_role', $this->role->getName());
    }

    /**
     * @covers ::createFromMetadata
     *
     * @param $data
     *   Invalid data.
     *
     * @dataProvider providerInvalidMetadata
     */
    public function testInvalidMetadata($data): void
    {
        $this->expectException(MetadataException::class);
        $this->createTestRole($data);
    }

    /**
     * Data provider for testInvalidMetadata().
     *
     * @return array[]
     */
    public function providerInvalidMetadata(): array
    {
        return [
            'nothing' => [[]],
            'no keyids' => [['threshold' => 1]],
            'no threshold' => [['keyids' => ['good_key']]],
            'invalid threshold' => [['threshold' => '1', 'keyids' => ['good_key']]],
            'invalid keyids' => [['threshold' => 1, 'keyids' => 'good_key_1,good_key_2']],
            'extra field' => [['threshold' => 1, 'keyids' => ['good_key'], 'extra_field' => 1]],
        ];
    }

    /**
     * @covers ::isKeyIdAcceptable
     *
     * @param string $keyId
     * @param bool $expected
     *
     * @dataProvider providerIsKeyIdAcceptable
     */
    public function testIsKeyIdAcceptable(string $keyId, bool $expected): void
    {
        self::assertSame($expected, $this->createTestRole()->isKeyIdAcceptable($keyId));
    }

    /**
     * Data provider for testIsKeyIdAcceptable().
     *
     * @return array[]
     */
    public function providerIsKeyIdAcceptable(): array
    {
        return [
            ['good_key_1', true],
            ['good_key_2', true],
            ['bad_key', false],
        ];
    }

    /**
     * Creates a test role.
     *
     * @param array|null $data
     *   The data for the role or null.
     *
     * @return \Tuf\Role
     */
    protected function createTestRole(?array $data = null): Role
    {
        $data = $data ?? [
            'threshold' => 1000,
            'keyids' => [
                'good_key_1',
                'good_key_2',
            ]
        ];
        return Role::createFromMetadata($data, 'my_role');
    }
}
