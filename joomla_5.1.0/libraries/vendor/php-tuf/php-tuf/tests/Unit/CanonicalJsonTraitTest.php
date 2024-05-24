<?php

namespace Tuf\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tuf\CanonicalJsonTrait;

/**
 * @coversDefaultClass \Tuf\CanonicalJsonTrait
 */
class CanonicalJsonTraitTest extends TestCase
{
    use CanonicalJsonTrait;

    /**
     * @covers ::sortKeys
     */
    public function testSort(): void
    {
        $fixturesDirectory = __DIR__ . '/../../fixtures/json';
        $sortedData = json_decode(file_get_contents("$fixturesDirectory/sorted.json"), true, 512, JSON_THROW_ON_ERROR);
        $unsortedData = json_decode(file_get_contents("$fixturesDirectory/unsorted.json"), true, 512, JSON_THROW_ON_ERROR);
        static::sortKeys($unsortedData);
        $this->assertSame($unsortedData, $sortedData);

        // Indexed arrays should not be sorted in alphabetical order, at any
        // level.
        $data = [
            // This must have at least 10 items, since that will be sorted
            // alphabetically (which is what we're trying to avoid).
            'b' => array_fill(0, 20, 'Hello!'),
            'a' => 'Canonically speaking, I go before b.',
            // This should be sorted, because PHP doesn't consider it a list.
            'c' => [
                3 => 'Hey',
                2 => 'Ho',
            ],
        ];
        // The associative keys should be in their original, non-canonical
        // order.
        $this->assertSame(['b', 'a', 'c'], array_keys($data));
        $this->assertTrue(array_is_list($data['b']));
        // Although 'c' has numeric keys, they're out of order and they don't
        // start from 0, so PHP should not consider 'c' a list, and its keys
        // should be sorted.
        $this->assertFalse(array_is_list($data['c']));
        $this->assertSame([3, 2], array_keys($data['c']));

        static::sortKeys($data);
        // The associative keys should be in canonical order now, and the
        // nested, indexed array should be unchanged.
        $this->assertSame(['a', 'b', 'c'], array_keys($data));
        $this->assertTrue(array_is_list($data['b']));
        $this->assertFalse(array_is_list($data['c']));
        $this->assertSame([2, 3], array_keys($data['c']));
    }

    public function testSortForListArrays(): void
    {
        // Indexed arrays should not be sorted in alphabetical order, at any
        // level.
        $data = [
            // Use an associative nested array
            0 => [
                'b' => 'Hey',
                'a' => 'Ho',
            ],
            // This should be sorted too, because PHP doesn't consider it a list.
            1 => [
                3 => 'Hey',
                2 => 'Ho',
            ],
        ];

        static::sortKeys($data);

        // The associative keys should be in canonical order now, and the
        // nested, indexed array should be unchanged.
        $this->assertSame([0,1], array_keys($data));
        $this->assertSame(['a', 'b'], array_keys($data['0']));
        $this->assertSame([2,3], array_keys($data[1]));
    }

    /**
     * @covers ::encodeJson
     */
    public function testSlashEscaping(): void
    {
        $json = static::encodeJson(['here/there' => 'everywhere']);
        $this->assertSame('{"here/there":"everywhere"}', $json);
    }
}
