<?php

namespace Tuf;

/**
 * Provides normalization to convert objects to canonical JSON strings.
 *
 * @todo This is a very incomplete implementation of
 *     http://wiki.laptop.org/go/Canonical_JSON.
 *     Consider creating a separate library under php-tuf just for this?
 *     https://github.com/php-tuf/php-tuf/issues/14
 *
 * @internal
 *   This is intended to be used only by PHP-TUF metadata classes.
 */
trait CanonicalJsonTrait
{
    /**
     * Encodes an associative array into a string of canonical JSON.
     *
     * @param array $data
     *   The associative array of data.
     *
     * @return string
     *   An encoded string of normalized, canonical JSON data.
     */
    protected static function encodeJson(array $data): string
    {
        static::sortKeys($data);
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Decodes JSON data to an associative array.
     *
     * @param string $data
     *   The JSON to decode.
     *
     * @return array
     *   The decoded data.
     */
    protected static function decodeJson(string $data): array
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Sorts an associative array into a canonical order.
     *
     * @param array $data
     *     The data to sort, passed by reference.
     *
     * @throws \RuntimeException
     *     Thrown if sorting the array fails.
     */
    protected static function sortKeys(array &$data): void
    {
        // Apply recursively on potential subarrays
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                static::sortKeys($data[$key]);
            }
        }

        // If $data is numerically indexed, the keys are already sorted, by
        // definition, no key sorting on this level necessary
        if (array_is_list($data)) {
            return;
        }

        if (!ksort($data, SORT_STRING)) {
            throw new \RuntimeException("Failure sorting keys. Canonicalization is not possible.");
        }
    }
}
