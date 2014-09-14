<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 29.07.2014
 * Time: 13:48
 */


namespace App\Helpers;

/**
 * Class ArraysHelper.
 * Provides various static helper methods to simplify array operating.
 * @package App\Helpers
 */
class ArraysHelper 
{
    /**
     * Returns array of random values from min to max values.
     *
     * @param $count
     * @param $min
     * @param $max
     * @return array
     */
    public static function getRandomArray($count, $min, $max) {
        $max = (int) $max;
        $min = (int) $min;
        $randomArray = array();
        if ($min > $max) {
            return [];
        }
        srand();
        // Limit the selected count to the max boundary.
        if ($count > $max - $min + 1) {
            $count = $max - $min + 1;
        }

        for ($i = 0; $i < $count;) {
            //Only append values which are not in the array.
            $value = rand($min, $max);
            if (in_array($value, $randomArray)) {
                continue;
            }
            $randomArray[] = $value;
            $i++;
        }
        return $randomArray;
    }

    /**
     * Merge several arrays in such way, that values with equal keys are not duplicated in result.
     * @return array
     */
    public static function arrayMergeRecursiveDistinct()
    {
        $arrays = func_get_args();
        foreach ($arrays as $key => $array) {
            $arrays[$key] = (array) $array;
        }

        $result = [];
        foreach ($arrays as $array) {
            $result = self::arrayMergeRecursiveDistinctWalker($result, $array);
        }

        return $result;
    }

    /**
     * Helper recursive walker for arrayMergeRecursiveDistinct().
     * Merges two arrays.
     * @see arrayMergeRecursiveDistinct()
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected static function arrayMergeRecursiveDistinctWalker(array $array1, array $array2)
    {
        $merged = $array1;

        if (count($array2) && self::onlyHasNumericKeys($merged)) {
            return $array2;
        }

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::arrayMergeRecursiveDistinctWalker($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Checks whether given array only has numeric keys.
     * @param $array
     * @return bool
     */
    private static function onlyHasNumericKeys($array)
    {
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                return false;
            }
        }

        return true;
    }

    public static function arrayFillEqualPairs(array $arr)
    {
        $values = array_values($arr);
        return array_combine($values, $values);
    }
} 