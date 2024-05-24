<?php

namespace Tuf\Tests\Metadata;

use Tuf\Exception\MetadataException;
use Tuf\Metadata\MetadataBase;
use Tuf\Metadata\RootMetadata;

/**
 * @coversDefaultClass \Tuf\Metadata\RootMetadata
 */
class RootMetadataTest extends MetadataBaseTest
{

    use UntrustedExceptionTrait;
    /**
     * {@inheritdoc}
     */
    protected $validJson = '1.root';

    /**
     * {@inheritdoc}
     */
    protected $expectedType = 'root';

    /**
     * {@inheritdoc}
     */
    protected static function callCreateFromJson(string $json): MetadataBase
    {
        return RootMetadata::createFromJson($json);
    }

    /**
     * {@inheritdoc}
     */
    public function providerExpectedField(): array
    {
        $data = parent::providerExpectedField();

        $data[] = ['signed:keys'];
        $firstKey = $this->getFixtureNestedArrayFirstKey('1.root', ['signed', 'keys']);
        $data[] = ["signed:keys:$firstKey:keytype"];
        $data[] = ["signed:keys:$firstKey:keyval"];
        $data[] = ["signed:keys:$firstKey:scheme"];
        $data[] = ['signed:roles'];
        $data[] = ['signed:roles:targets:keyids'];
        $data[] = ['signed:roles:targets:threshold'];
        return static::getKeyedArray($data);
    }

    /**
     * {@inheritdoc}
     */
    public function providerValidField(): array
    {
        $data = parent::providerValidField();
        $firstKey = $this->getFixtureNestedArrayFirstKey($this->validJson, ['signed', 'keys']);
        $data[] = ["signed:keys:$firstKey:keytype", 'string'];
        $data[] = ["signed:keys:$firstKey:keyval", 'array'];
        $data[] = ["signed:keys:$firstKey:keyval:public", 'string'];
        $data[] = ["signed:keys:$firstKey:scheme", 'string'];
        $data[] = ['signed:roles', 'array'];
        $data[] = ['signed:roles:targets:keyids', 'array'];
        $data[] = ['signed:roles:targets:threshold', 'int'];
        $data[] = ['signed:consistent_snapshot', 'boolean'];
        return static::getKeyedArray($data);
    }

    /**
     * Tests that an exception will thrown if a required role is missing.
     *
     * @param string $missingRole
     *   The required role to test.
     *
     * @return void
     *
     * @dataProvider providerRequireRoles
     */
    public function testRequiredRoles(string $missingRole): void
    {
        $this->expectException(MetadataException::class);
        $expectedMessage = preg_quote("Array[signed][roles][$missingRole]:", '/');
        $expectedMessage .= '.*This field is missing';
        $this->expectExceptionMessageMatches("/$expectedMessage/s");
        $data = json_decode($this->clientStorage->read($this->validJson), true);
        unset($data['signed']['roles'][$missingRole]);
        static::callCreateFromJson(json_encode($data));
    }

    /**
     * Dataprovider for testRequiredRoles().
     *
     * @return string[][]
     *   The test cases.
     */
    public function providerRequireRoles(): array
    {
        return static::getKeyedArray([
            ['root'],
            ['timestamp'],
            ['snapshot'],
            ['targets'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function providerOptionalFields(): array
    {
        $data = parent::providerOptionalFields();
        $data[] = [
            'signed:roles:mirror',
            [
                'keyids' => ['76b9ae56adaeebe44ebfd4e73c57bb68e920ee046ff03c6f7e1424a9078af785'],
                'threshold' => 1,
            ],
        ];
        $data[] = ['signed:consistent_snapshot', true];
        return static::getKeyedArray($data, 0);
    }

    /**
     * Tests that an unknown role name is not allowed.
     *
     * @return void
     */
    public function testInvalidRoleName(): void
    {
        $this->expectException(MetadataException::class);
        $expectedMessage = preg_quote("Array[signed][roles][super_root]:", '/');
        $expectedMessage .= '.*This field was not expected';
        $this->expectExceptionMessageMatches("/$expectedMessage/s");
        $data = json_decode($this->clientStorage->read($this->validJson), true);
        $data['signed']['roles']['super_root'] = $data['signed']['roles']['root'];
        static::callCreateFromJson(json_encode($data));
    }

    /**
     * @covers ::supportsConsistentSnapshots
     *
     * @return void
     */
    public function testSupportsConsistentSnapshots(): void
    {
        $data = json_decode($this->clientStorage->read($this->validJson), true);
        foreach ([true, false] as $value) {
            $data['signed']['consistent_snapshot'] = $value;
            /** @var \Tuf\Metadata\RootMetadata $metadata */
            $metadata = static::callCreateFromJson(json_encode($data));
            $metadata->trust();
            $this->assertSame($value, $metadata->supportsConsistentSnapshots());
        }
    }

    /**
     * Data provider for testUntrustedException().
     *
     * @return string[]
     *   The test cases for testUntrustedException().
     */
    public function providerUntrustedException(): array
    {
        return self::getKeyedArray([
            ['supportsConsistentSnapshots'],
            ['getKeys'],
            ['getRoles'],
        ]);
    }

    /**
     * @covers ::getRoles
     */
    public function testGetRoles(): void
    {
        $json = $this->clientStorage->read($this->validJson);
        $data = json_decode($json, true);
        /** @var \Tuf\Metadata\RootMetadata $metadata */
        $metadata = static::callCreateFromJson($json);
        $metadata->trust();
        $expectRoleNames = ['root', 'snapshot', 'targets', 'timestamp'];
        $roles = $metadata->getRoles();
        self::assertCount(4, $roles);
        foreach ($expectRoleNames as $expectRoleName) {
            self::assertSame($data['signed']['roles'][$expectRoleName]['threshold'], $roles[$expectRoleName]->getThreshold());
            self::assertSame($expectRoleName, $roles[$expectRoleName]->getName());
            foreach ($data['signed']['roles'][$expectRoleName]['keyids'] as $keyId) {
                self::assertTrue($roles[$expectRoleName]->isKeyIdAcceptable($keyId));
            }
            self::assertFalse($roles[$expectRoleName]->isKeyIdAcceptable('nobodys_key'));
        }
    }

    /**
     * Test that keyid_hash_algorithms must equal the exact value.
     *
     * @see \Tuf\Metadata\ConstraintsTrait::getKeyConstraints()
     */
    public function testKeyidHashAlgorithms()
    {
        $json = $this->clientStorage->read($this->validJson);
        $data = json_decode($json, true);
        $keyId = key($data['signed']['keys']);
        $data['signed']['keys'][$keyId]['keyid_hash_algorithms'][1] = 'sha513';
        self::expectException(MetadataException::class);
        $expectedMessage = preg_quote("Array[signed][keys][$keyId][keyid_hash_algorithms]:", '/');
        $expectedMessage .= '.* This value should be equal to array';
        self::expectExceptionMessageMatches("/$expectedMessage/s");
        static::callCreateFromJson(json_encode($data));
    }

    public function testInvalidKeyType(): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $keyId = key($metadata['signed']['keys']);
        $metadata['signed']['keys'][$keyId]['keytype'] = 'invalid key type';
        $expectedMessage = preg_quote("Array[signed][keys][$keyId][keytype]", '/');
        $expectedMessage .= ".*This value should be identical to string \"ed25519\"";
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches("/$expectedMessage/s");
        static::callCreateFromJson(json_encode($metadata));
    }
}
