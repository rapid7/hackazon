<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 25.08.2014
 * Time: 17:45
 */


namespace App\Core;


use PHPixie\Controller;
use VulnModule\VulnInjection\Service as VulnService;

/**
 * Base controller for standard and REST controllers
 * @package App\Core
 * @inheritdoc
 * @property-read \App\Pixie $pixie Pixie dependency container
 * @property-read \App\Core\Request $request Pixie dependency container
 */
class BaseController extends Controller
{
    /**
     * @var VulnService
     */
    protected $vulninjection;

    public function before()
    {
        $className = $this->get_real_class($this);
        $controllerName = strtolower($className);

        // Create vulnerability service.
        $this->vulninjection = $this->pixie->vulninjection->service($controllerName);
        $this->pixie->setVulnService($this->vulninjection);

        // Check referrer for system-wide level
        $this->vulninjection->checkReferrer();

        // Switch vulnerability config to the controller level
        $this->vulninjection->goDown($controllerName);
    }

    public function after()
    {
        // Exit controller-level vulnerability context.
        $this->vulninjection->goUp();
    }

    /**
     * Obtains an object class name without namespaces
     */
    public function get_real_class($obj) {
        $classname = get_class($obj);

        if (preg_match('@\\\\(?<class_name>[\w]+)$@', $classname, $matches)) {
            $classname = $matches['class_name'];
        }

        return $classname;
    }

    /**
     * var_dump beautiful dump.
     */
    public function dumpx()
    {
        call_user_func_array([$this->pixie->debug, 'dumpx'], func_get_args());
    }

    /**
     * Dump and exit script.
     */
    public function dump()
    {
        call_user_func_array([$this->pixie->debug, 'dump'], func_get_args());
    }

    /**
     * Generates URL by given name and parameters.
     *
     * @param string $route Route name
     * @param array $params controller, action, and so on
     * @param bool $absolute Whether link is absolute or not
     * @param string $protocol
     * @return string
     */
    public function generateUrl($route = 'default', array $params = array(), $absolute = false, $protocol = 'http')
    {
        if (!isset($params['action'])) {
            $params['action'] = false;
        }
        return $this->pixie->router->get($route)->url($params, $absolute, $protocol);
    }
} 