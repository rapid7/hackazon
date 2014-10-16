<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:13
 */

namespace AmfphpModule\Core;

use App\IPixifiable;
use App\Pixie;

class Config extends \Amfphp_Core_Config implements IPixifiable
{
    /**
     * @var Pixie
     */
    protected $pixie;

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

    function getPixie()
    {
        return $this->pixie;
    }

    function setPixie(Pixie $pixie = null)
    {
        $this->pixie = $pixie;
    }
} 