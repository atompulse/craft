<?php

namespace Craft\Data\Processor;

/**
 * Class ArrayProcessor
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ArrayProcessor
{
    /**
     * Given a complex array will create a simple array using as key/value the values from the original array
     * @param $data
     * @param $key
     * @param $value
     * @return array
     */
    public static function remapArrData(array $data, $key, $value): array
    {
        /*
        Array
        (
            [1] => Array
                (
                    [value] => 1
                    [name] => NotSet
                    [label] => N/A
                )

            [2] => Array
                (
                    [value] => 2
                    [name] => Other
                    [label] => Altele
                )
        )
        remapped by (value/name):
        Array
        (
            1 => NotSet,
            2 => Other
        )
        */

        $result = [];
        foreach ($data as $item) {
            $result[$item[$key]] = $item[$value];
        }

        return $result;
    }

    /**
     * An array of key/value pairs to be flipped.
     * Exchanges all keys with their associated values in an array
     * @note The difference to the php function is that if a value has several occurrences,
     * then this function will build an array for all those occurrences so no values are lost
     * @param array $data
     * @return array
     */
    public static function arrayFlip(array $data): array
    {
        $dataFlipped = [];

        $metaData = array_count_values($data);

        foreach ($data as $key => $value) {
            if ($metaData[$value] > 1) {
                $dataFlipped[$value][] = $key;
            } else {
                $dataFlipped[$value] = $key;
            }
        }

        return $dataFlipped;
    }

    /**
     * Transform an array's keys from camelCase to under_score
     * @param array $data
     * @return array
     */
    public static function fromCamelToUnder(array $data): array
    {
        $transformed = [];
        foreach ($data as $camelKey => $value) {
            $transformed[StringProcessor::unCamelize($camelKey)] = $value;
        }

        return $transformed;
    }

    /**
     * Transform arrays keys from camelCase to under_score
     * @param array $arrData
     * @return array
     */
    public static function arrayFromCamelToUnder(array $arrData): array
    {
        $transformed = [];
        foreach ($arrData as $index => $data) {
            foreach ($data as $camelKey => $value) {
                $transformed[$index][StringProcessor::unCamelize($camelKey)] = $value;
            }
        }

        return $transformed;
    }

    /**
     * Filter an array with string values by a starting string
     * @param array $array
     * @param string $startingWith
     * @return array
     */
    public static function filterArrayValuesStartingWith(array $array, string $startingWith)
    {
        return array_filter($array, function ($url) use ($startingWith) {
            return (stripos($url, $startingWith) !== false && stripos($url, $startingWith) === 0);
        });
    }
}
