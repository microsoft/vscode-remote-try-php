<?php

namespace Tuf\Tests\Metadata;

use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\TimestampMetadata;

/**
 * @coversDefaultClass \Tuf\Metadata\TimestampMetadata
 */
class TimestampMetadataTest extends MetadataBaseTest
{
    use UntrustedExceptionTrait;

    /**
     * {@inheritdoc}
     */
    protected $validJson = '1.timestamp';

    /**
     * {@inheritdoc}
     */
    protected $expectedType = 'timestamp';

    /**
     * {@inheritdoc}
     */
    protected static function callCreateFromJson(string $json): MetadataBase
    {
        return TimestampMetadata::createFromJson($json);
    }

    /**
     * {@inheritdoc }
     */
    public function providerOptionalFields(): array
    {
        $data = parent::providerOptionalFields();
        $data[] = ['signed:meta:snapshot.json:length', 999];
        return static::getKeyedArray($data);
    }

    /**
     * {@inheritdoc}
     */
    public function providerExpectedField(): array
    {
        $data = parent::providerExpectedField();
        $data[] = ['signed:meta'];
        $data[] = ['signed:meta:snapshot.json', 'This collection should contain 1 element or more.'];
        $data[] = ['signed:meta:snapshot.json:version'];
        $data[] = ['signed:meta:snapshot.json:hashes'];
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function providerValidField(): array
    {
        $data = parent::providerValidField();
        $data[] = ['signed:meta', 'array'];
        $data[] = ['signed:meta:snapshot.json', 'array'];
        $data[] = ['signed:meta:snapshot.json:version', 'int'];
        $data[] = ['signed:meta:snapshot.json:length', 'int'];
        $data[] = ['signed:meta:snapshot.json:hashes', 'array'];
        $data[] = ['signed:meta:snapshot.json:hashes:sha256', 'string'];
        $data[] = ['signed:meta:snapshot.json:hashes:sha512', 'string'];
        return $data;
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
