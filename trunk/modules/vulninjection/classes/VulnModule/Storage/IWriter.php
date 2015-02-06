<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.11.2014
 * Time: 11:40
 */

namespace VulnModule\Storage;


use VulnModule\Config\Context;

interface IWriter {
    public function write(Context $context);
}