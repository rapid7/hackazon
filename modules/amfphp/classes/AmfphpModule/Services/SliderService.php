<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 09.10.2014
 * Time: 14:39
 */


namespace AmfphpModule\Services;


class SliderService 
{
    /**
     * @param int $num
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getSlides($num = 0)
    {
        $slides = [
            '/images/banner_01-v3.jpg',
            '/images/banner_02-v3.jpg',
            '/images/banner_03-v3.jpg',
            '/images/banner_04-v3.jpg',
        ];

        if (!is_numeric($num) || (int)$num < 1 || (int)$num > count($slides)) {
            throw new \InvalidArgumentException();
        }

        $num = (int)$num;
        return array_slice($slides, 0, $num);
    }
} 