<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 09.10.2014
 * Time: 14:39
 */


namespace AmfphpModule\Services;


use AmfphpModule\Service;
use VulnModule\Config\Annotations as Vuln;

/**
 * Processes slides functionality.
 * @package AmfphpModule\Services
 * @Vuln\Description("Service used to operate slides in flash banner.")
 */
class SliderService extends Service
{
    /**
     * @param int $num
     * @return array
     * @throws \InvalidArgumentException
     * @Vuln\Description("Fetches the given number of slides to show in banner.")
     */
    public function getSlides($num = 0)
    {
        $num = $this->wrap('num', $num)->getFilteredValue();

        $slides = [
            '/images/banner_01-v3.jpg',
            '/images/banner_02-v3.jpg',
            '/images/banner_03-v3.jpg',
            '/images/banner_04-v3.jpg',
        ];

        if (!is_numeric($num) || (int)$num < 1 || (int)$num > count($slides)) {
            throw new \InvalidArgumentException("Invalid slides number: " . $num);
        }

        $num = (int)$num;
        return array_slice($slides, 0, $num);
    }
}