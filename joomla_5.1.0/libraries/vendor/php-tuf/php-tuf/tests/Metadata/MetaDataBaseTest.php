<?php

namespace Tuf\Tests\Metadata;

use PHPUnit\Framework\TestCase;
use Tuf\CanonicalJsonTrait;
use Tuf\Exception\MetadataException;
use Tuf\Metadata\MetadataBase;
use Tuf\Tests\TestHelpers\FixturesTrait;
use Tuf\Tests\TestHelpers\UtilsTrait;

/**
 * @coversDefaultClass \Tuf\Metadata\MetadataBase
 */
abstract class MetadataBaseTest extends TestCase
{
    use CanonicalJsonTrait;
    use FixturesTrait;
    use UtilsTrait;

    /**
     * The client-side metadata storage.
     *
     * @var \Tuf\Tests\TestHelpers\DurableStorage\TestStorage
     */
    protected $clientStorage;

    /**
     * The valid json file.
     *
     * @var string
     */
    protected $validJson;

    /**
     * The expected metadata type;
     * @var string
     */
    protected $expectedType;


    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientStorage = static::loadFixtureIntoMemory('Delegated/consistent');
    }

    /**
     * Calls createFromJson() for the test class.
     *
     * @param string $json
     *   The json string.
     *
     * @return void
     *
     * @throws \Tuf\Exception\MetadataException
     *   If validation fails.
     */
    abstract protected static function callCreateFromJson(string $json): MetadataBase;

    /**
     * Tests for valid metadata.
     *
     * @param string $validJson
     *   The valid json key from $this->clientStorage.
     *
     * @return void
     *
     * @dataProvider providerValidMetadata
     */
    public function testValidMetadata(string $validJson): void
    {
        static::callCreateFromJson($this->clientStorage->read($validJson));
    }

    /**
     * Dataprovider for testValidMetadata().
     *
     * @return \string[][]
     *   The nested array containing all client fixture files for the type.
     *
     */
    public function providerValidMetadata(): array
    {
        $fixturesDir = static::getFixturePath('Delegated/consistent', 'client/metadata/current');
        $files = glob("$fixturesDir/*.{$this->expectedType}.json");
        if (empty($files)) {
            throw new \RuntimeException('No fixtures files found for ' . $this->expectedType);
        }
        $data[] = [$this->expectedType];
        foreach ($files as $file) {
            $data[] = [basename($file, '.json')];
        }
        return static::getKeyedArray($data);
    }

    /**
     * Tests that validation fails on invalid type.
     *
     *  @return void
     */
    public function testInvalidType(): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $metadata['signed']['_type'] = 'invalid_type_value';
        $expectedMessage = preg_quote("Array[signed][_type]", '/');
        $expectedMessage .= ".*This value should be equal to \"{$this->expectedType}\"";
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches("/$expectedMessage/s");
        static::callCreateFromJson(json_encode($metadata));
    }

    /**
     * @covers ::getType
     *
     *  @return void
     */
    public function testGetType(): void
    {
        $metadata = static::callCreateFromJson($this->clientStorage->read($this->validJson));
        $this->assertSame($metadata->getType(), $this->expectedType);
    }

    /**
     * @covers ::getRole
     *
     *  @return void
     */
    public function testGetRole(): void
    {
        $metadata = static::callCreateFromJson($this->clientStorage->read($this->validJson));
        $this->assertSame($this->expectedType, $metadata->getRole());
    }

    /**
     * Tests valid and invalid expires dates.
     *
     * @param string $expires
     *   Expires date to test.
     * @param boolean $valid
     *   Whether it's valid.
     *
     *  @return void
     *
     * @dataProvider providerExpires
     */
    public function testExpires(string $expires, bool $valid): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $metadata['signed']['expires'] = $expires;
        if (!$valid) {
            $expectedMessage = preg_quote('Array[signed][expires]', '/');
            $expectedMessage .= '.*This value is not a valid datetime.';
            $this->expectException(MetadataException::class);
            $this->expectExceptionMessageMatches("/$expectedMessage/s");
        }
        static::callCreateFromJson(json_encode($metadata));
    }

    /**
     * Tests valid and invalid spec versions.
     *
     * @param string $version
     *   Spec version to test.
     * @param boolean $valid
     *   Whether it's valid.
     *
     *  @return void
     *
     * @dataProvider providerSpecVersion
     */
    public function testSpecVersion(string $version, bool $valid): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $metadata['signed']['spec_version'] = $version;
        if (!$valid) {
            $expectedMessage = preg_quote('Array[signed][spec_version]', '/');
            $expectedMessage .= '.*This value is not valid.';
            $this->expectException(MetadataException::class);
            $this->expectExceptionMessageMatches("/$expectedMessage/s");
        }
        static::callCreateFromJson(json_encode($metadata));
    }

    /**
     * Tests for metadata with a missing field.
     *
     * @param string $expectedField
     *   The name of the field. Nested fields indicated with ":".
     *
     * @param string|null $exception
     *
     *   A different exception message to expect.
     *
     * @return void
     *
     * @dataProvider providerExpectedField
     */
    public function testMissingField(string $expectedField, string $exception = null): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $keys = explode(':', $expectedField);
        $fieldName = preg_quote('Array[' . implode('][', $keys) . ']', '/');
        $this->nestedUnset($keys, $metadata);
        $json = json_encode($metadata);
        $this->expectException(MetadataException::class);
        if ($exception) {
            $this->expectExceptionMessageMatches("/$exception/s");
        } else {
            $this->expectExceptionMessageMatches("/$fieldName.*This field is missing./s");
        }
        static::callCreateFromJson($json);
    }

    /**
     * Tests allowed optional fields.
     *
     * @param string $optionalField
     *   The name of the field. Nested fields indicated with ":".
     * @param mixed $value
     *   The value to set.
     *
     * @return void
     *
     * @dataProvider providerOptionalFields
     */
    public function testOptionalFields(string $optionalField, $value): void
    {
        $optionalField = explode(':', $optionalField);

        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        static::nestedChange($optionalField, $metadata, $value);
        $this->assertDataIsValid($metadata);

        // If the field is truly optional, we should be able to delete it.
        $this->nestedUnset($optionalField, $metadata);
        $this->assertDataIsValid($metadata);
    }

    /**
     * Asserts that a metadata object can be created from JSON-encoded data.
     *
     * @param array $data
     *   The data which will be encoded as JSON and used to create the metadata
     *   object.
     */
    private function assertDataIsValid(array $data): void
    {
        $json = json_encode($data);
        static::assertInstanceOf(MetadataBase::class, static::callCreateFromJson($json));
    }

    /**
     * Dataprovider for testOptionalFields().
     *
     * @return mixed[]
     *   The test cases for testOptionalFields().
     */
    public function providerOptionalFields(): array
    {
        return static::getKeyedArray([
            ['signed:ignored_value', 1],
        ]);
    }

    /**
     * Unset a nested array element.
     *
     * @param array $keys
     *   Ordered keys to the value to unset.
     * @param array $data
     *   The array to modify.
     *
     * @return void
     */
    protected function nestedUnset(array $keys, array &$data): void
    {
        $key = array_shift($keys);
        if ($keys) {
            $this->nestedUnset($keys, $data[$key]);
        } else {
            unset($data[$key]);
        }
    }

    /**
     * Tests for metadata with a field of invalid type.
     *
     * @param string $expectedField
     *   The name of the field. Nested fields indicated with ":".
     *
     * @param string $expectedType
     *   The type of the field.
     *
     * @return void
     *
     * @dataProvider providerValidField
     */
    public function testInvalidField(string $expectedField, string $expectedType): void
    {
        $metadata = json_decode($this->clientStorage->read($this->validJson), true);
        $keys = explode(':', $expectedField);

        switch ($expectedType) {
            case 'string':
                $newValue = [];
                break;
            case 'int':
                $newValue = 'Abb';
                break;
            case 'array':
                $newValue = 3060;
                break;
            case 'boolean':
                $newValue = 'this is a string';
                break;
            case '\ArrayObject':
                $newValue = 'Not an ArrayObject';
                break;
        }

        static::nestedChange($keys, $metadata, $newValue);
        $json = json_encode($metadata);
        $this->expectException(MetadataException::class);
        $this->expectExceptionMessageMatches("/This value should be of type " . preg_quote($expectedType) . "/s");
        static::callCreateFromJson($json);
    }

    /**
     * Dataprovider for testExpires().
     *
     * @return array
     *   Array of arrays of expires, and whether it should be valid.
     */
    public function providerExpires(): array
    {
        return static::getKeyedArray([
            ['1970', false],
            ['1970-01-01T00:00:00Z', true],
            ['2000-01-01', false],
            ['2000-01-01T00:00:00', false],
            ['3000-01-01T00:00:00Z', true],
            ['2000-01-01T00:00:61Z', false],
            ['2000-01-01T24:00:01Z', false],
            ['2000-01-01T00:00:00Z', true],
            ['2030-01-01T20:50:10Z', true],
            ['2030-11-01T20:50:10Z', true],
            ['2330-12-21T20:50:10Z', true],
        ]);
    }

    /**
     * Dataprovider for testSpecVersion().
     *
     * @return array
     *   Array of arrays of spec version, and whether it should be valid.
     */
    public function providerSpecVersion(): array
    {
        return [
            ['1', false],
            ['1.0', true],
            ['1.9', true],
            ['1.99', true],
            ['1.999', true],
            ['2.00', false],
            ['1.0.a', false],
            ['1.0.1', true],
            ['1.99.8', true],
            ['1.1.1', true],
        ];
    }

    /**
     * Dataprovider for testMissingField().
     *
     * @return array
     *   Array of arrays of expected field name, and optional exception message.
     */
    public function providerExpectedField(): array
    {
        return [
            ['signed'],
            ['signed:_type'],
            ['signed:expires'],
            ['signed:spec_version'],
            ['signed:version'],
            ['signatures'],
            ['signatures:0:keyid'],
            ['signatures:0:sig'],
        ];
    }

    /**
     * Dataprovider for testInvalidField().
     *
     * @return array
     *   Array of arrays of expected field name, and field data type.
     */
    public function providerValidField(): array
    {
        return [
            ['signed', 'array'],
            ['signed:_type', 'string'],
            ['signed:expires', 'string'],
            ['signed:spec_version', 'string'],
            ['signed:version', 'int'],
            ['signatures', 'array'],
            ['signatures:0:keyid', 'string'],
            ['signatures:0:sig', 'string'],
        ];
    }

    /**
     * Determines the first key for the 'keys' element to avoid the test
     * breaking every time the test fixtures are recreated.
     *
     * @param string $fixtureName
     *   The fixture file name.
     * @param array $nestedKeys
     *   The keys to access the array.
     *
     * @return string
     *   The first key.
     */
    protected function getFixtureNestedArrayFirstKey(string $fixtureName, array $nestedKeys): string
    {
        $realPath = static::getFixturePath('Delegated/consistent', "client/metadata/current/$fixtureName.json", false);
        $data = json_decode(file_get_contents($realPath), true);
        foreach ($nestedKeys as $nestedKey) {
            $data = $data[$nestedKey];
        }
        $keys = array_keys($data);
        return array_shift($keys);
    }

    /**
     * @covers ::toCanonicalJson
     *
     * @param string $validJson
     *   The valid json key from $this->clientStorage.
     *
     * @dataProvider providerValidMetadata
     */
    public function testNormalization(string $validJson): void
    {
        $contents = $this->clientStorage->read($validJson);
        $json = json_decode($contents);
        $metadata = static::callCreateFromJson($contents);
        $this->assertEquals(json_encode($json->signed), $metadata->toCanonicalJson());
    }

    public function testDuplicateKeyId(): void
    {
        $json = $this->clientStorage->read($this->validJson);
        $data = static::decodeJson($json);
        $this->assertNotEmpty($data['signatures']);
        $data['signatures'][] = [
            'keyid' => $data['signatures'][0]['keyid'],
            'sig' => 'In real metadata, this would be a hash digest.',
        ];
        $json = static::encodeJson($data);

        $this->expectException(MetadataException::class);
        $this->expectExceptionMessage('Key IDs must be unique.');
        static::callCreateFromJson($json);
    }
}
