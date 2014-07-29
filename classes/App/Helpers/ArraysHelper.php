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
} 