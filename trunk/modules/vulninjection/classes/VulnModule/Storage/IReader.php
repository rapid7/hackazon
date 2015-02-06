<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.11.2014
 * Time: 11:40
 */

namespace VulnModule\Storage;


use VulnModule\Config\Context;

/**
 * Reads serialized context data from certain source
 * @package VulnModule\Context
 */
interface IReader {
    /**
     * @param $name
     * @return Context
     */
    public function read($name);

    /**
     * @return array
     */
    public function getOwnContextNames();

    /**
     * @return array
     */
    public function getReferenceContextNames();

    /**
     * @return array
     */
    public function getAllContextNames();
}