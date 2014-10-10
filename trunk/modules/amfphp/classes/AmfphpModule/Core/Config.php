<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:13
 */

namespace AmfphpModule\Core;

use App\Pixie;
use App\Traits\Pixifiable;

class Config extends \Amfphp_Core_Config
{
    use Pixifiable;

    public function __construct(Pixie $pixie)
    {
        parent::__construct();
        $this->pluginsConfig['AmfphpDiscovery']['restrictAccess'] = false;
        $this->serviceFolders = [
            [dirname(__FILE__) . '/../Services/', '\\AmfphpModule\\Services']
        ];
        $this->pluginsFolders[] = dirname(__FILE__) . '/../Plugins/';
        $this->pluginsConfig['Pixifier']['pixie'] = $pixie;
    }
} 