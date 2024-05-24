<?php


namespace Tuf\Tests\TestHelpers;

/**
 * General test utility helper trait.
 */
trait UtilsTrait
{

    /**
     * Helper methods for dataProvider methods to return keyed arrays.
     *
     * @param array $providedData
     *   The dataProvider data.
     *
     * @param integer|null $useArgumentNumber
     *   (optional) The argument to user the key.
     *
     * @return array
     *   The new keyed array where the keys are string concatenation of the
     *   arguments.
     */
    protected static function getKeyedArray(array $providedData, int $useArgumentNumber = null): array
    {
        $newData = [];
        foreach ($providedData as $arguments) {
            $key = '';
            if ($useArgumentNumber !== null) {
                $key = (string) $arguments[$useArgumentNumber];
            } else {
                foreach ($arguments as $argument) {
                    if (is_numeric($argument) || is_string($argument)) {
                        $key .= '-' . (string) $argument;
                    }
                }
            }

            if (isset($newData[$key])) {
                throw new \RuntimeException("Cannot produce unique keys");
            }
            $newData[$key] = $arguments;
        }
        return $newData;
    }

    /**
     * Change a nested array element.
     *
     * @param array $keys
     *   Ordered keys to the value to set.
     * @param array $data
     *   The array to modify.
     * @param mixed $newValue
     *   The new value to set.
     *
     * @return void
     */
    protected static function nestedChange(array $keys, array &$data, $newValue): void
    {
        $key = array_shift($keys);
        if ($keys) {
            static::nestedChange($keys, $data[$key], $newValue);
        } else {
            $data[$key] = $newValue;
        }
    }
}
