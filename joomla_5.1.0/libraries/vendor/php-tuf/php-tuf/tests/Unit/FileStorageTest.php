<?php

namespace Tuf\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Tuf\Client\DurableStorage\FileStorage;
use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\RootMetadata;
use Tuf\Metadata\SnapshotMetadata;
use Tuf\Metadata\TargetsMetadata;
use Tuf\Metadata\TimestampMetadata;

/**
 * @coversDefaultClass \Tuf\Client\DurableStorage\FileStorage
 */
class FileStorageTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Tests creating a FileStorage object with an invalid directory.
     *
     * @covers ::__construct
     */
    public function testCreateWithInvalidDirectory(): void
    {
        $dir = '/nonsensedirdoesnotexist' . uniqid();
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage("Cannot initialize filesystem local state: '$dir' is not a directory.");
        new FileStorage($dir);
    }

    /**
     * @covers ::getRoot
     * @covers ::getTimestamp
     * @covers ::getSnapshot
     * @covers ::getTargets
     */
    public function testLoadTrustedMetadata(): void
    {
        $storage = new FileStorage(__DIR__ . '/../../fixtures/Delegated/consistent/client/metadata/current');

        $metadata = $storage->getRoot();
        $this->assertInstanceOf(RootMetadata::class, $metadata);
        $metadata->ensureIsTrusted();

        $metadata = $storage->getTimestamp();
        $this->assertInstanceOf(TimestampMetadata::class, $metadata);
        $metadata->ensureIsTrusted();

        $metadata = $storage->getSnapshot();
        $this->assertInstanceOf(SnapshotMetadata::class, $metadata);
        $metadata->ensureIsTrusted();

        $metadata = $storage->getTargets();
        $this->assertInstanceOf(TargetsMetadata::class, $metadata);
        $metadata->ensureIsTrusted();
        $this->assertSame('targets', $metadata->getRole());

        $metadata = $storage->getTargets('unclaimed');
        $this->assertInstanceOf(TargetsMetadata::class, $metadata);
        $metadata->ensureIsTrusted();
        $this->assertSame('unclaimed', $metadata->getRole());
    }

    public function providerMetadataStorage(): array
    {
        return [
            'root' => [
                RootMetadata::class,
                'root',
                'root.json',
            ],
            'timestamp' => [
                TimestampMetadata::class,
                'timestamp',
                'timestamp.json',
            ],
            'snapshot' => [
                SnapshotMetadata::class,
                'snapshot',
                'snapshot.json',
            ],
            'targets' => [
                TargetsMetadata::class,
                'targets',
                'targets.json',
            ],
            'delegated role' => [
                TargetsMetadata::class,
                'delegated',
                'delegated.json',
            ],
        ];
    }

    /**
     * Tests storing metadata with a FileStorage object.
     *
     * @covers ::save
     * @covers ::delete
     *
     * @dataProvider providerMetadataStorage
     *
     * @testdox Writing and deleting $_dataName metadata
     */
    public function testWriteMetadata(string $metadataClass, string $role, string $expectedFileName): void
    {
        $dir = sys_get_temp_dir();
        $storage = new FileStorage($dir);

        $metadata = $this->prophesize($metadataClass);
        $metadata->getRole()->willReturn($role);
        $metadata->getSource()->willReturn("From hell's heart, I refactor thee!");
        $metadata->ensureIsTrusted()->shouldBeCalled();
        $storage->save($metadata->reveal());

        $filePath = $dir . '/' . $expectedFileName;
        $this->assertFileExists($filePath);
        $this->assertSame("From hell's heart, I refactor thee!", file_get_contents($filePath));

        $storage->delete($role);
        $this->assertFileDoesNotExist($filePath);

        // Trying to load non-existent metadata should return null, except for
        // root metadata, which throws an exception.
        $this->assertNull($storage->getTimestamp());
        $this->assertNull($storage->getSnapshot());
        $this->assertNull($storage->getTargets());
        $this->expectException('LogicException');
        $this->expectExceptionMessage('Could not load root metadata.');
        $storage->getRoot();
    }

    public function testFileNamesAreUrlEncoded(): void
    {
        $roleName = 'hello/../there!';
        $fileContents = '{["A test of your mettle"]}';

        $dir = sys_get_temp_dir();
        $storage = new FileStorage($dir);

        $metadata = $this->prophesize(MetadataBase::class);
        $metadata->getRole()->willReturn($roleName);
        $metadata->getSource()->willReturn($fileContents);
        $metadata->ensureIsTrusted()->shouldBeCalled();
        $storage->save($metadata->reveal());

        $expectedFileName = 'hello%2F..%2Fthere%21.json';
        $this->assertFileExists($dir . '/' . $expectedFileName);

        $method = new \ReflectionMethod($storage, 'read');
        $method->setAccessible(true);
        $this->assertSame($fileContents, $method->invoke($storage, $roleName));
    }
}
