<?php

namespace Tuf\Tests\Metadata;

use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\SnapshotMetadata;

/**
 * @coversDefaultClass \Tuf\Metadata\SnapshotMetadata
 */
class SnapshotMetadataTest extends MetadataBaseTest
{
    use UntrustedExceptionTrait;

    /**
     * {@inheritdoc}
     */
    protected $validJson = '1.snapshot';

    /**
     * {@inheritdoc}
     */
    protected $expectedType = 'snapshot';

    /**
     * {@inheritdoc}
     */
    protected static function callCreateFromJson(string $json): MetadataBase
    {
        return SnapshotMetadata::createFromJson($json);
    }
    /**
     * {@inheritdoc}
     */
    public function providerExpectedField(): array
    {
        $data = parent::providerExpectedField();
        $data[] = ['signed:meta'];
        $data[] = ['signed:meta:targets.json', 'This collection should contain 1 element or more.'];
        $data[] = ['signed:meta:targets.json:version'];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerValidField(): array
    {
        $data = parent::providerValidField();
        $data[] = ['signed:meta', 'array'];
        $data[] = ['signed:meta:targets.json', 'array'];
        $data[] = ['signed:meta:targets.json:version', 'int'];
        return $data;
    }

    /**
     * {@inheritdoc }
     */
    public function providerOptionalFields(): array
    {
        $data = parent::providerOptionalFields();
        $data[] = ['signed:meta:targets.json:length', 999];
        $data[] = ['signed:meta:targets.json:hashes', ['sha256' => 'some long hash']];
        return static::getKeyedArray($data);
    }

    /**
     * Data provider for testUntrustedException().
     *
     * @return string[]
     *   The test cases for testUntrustedException().
     */
    public function providerUntrustedException(): array
    {
        $mockMetadata = $this->createMock(MetadataBase::class);
        return self::getKeyedArray([
            ['getFileMetaInfo', ['any-key']],
        ]);
    }
}
