<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:13
 */

namespace AmfphpModule\Core;

class Config extends \Amfphp_Core_Config
{
    public function __construct()
    {
        parent::__construct();
        $this->pluginsConfig['AmfphpDiscovery']['restrictAccess'] = false;
        $this->serviceFolders = [
            [dirname(__FILE__) . '/../Services/', '\\AmfphpModule\\Services']
        ];
    }
} 