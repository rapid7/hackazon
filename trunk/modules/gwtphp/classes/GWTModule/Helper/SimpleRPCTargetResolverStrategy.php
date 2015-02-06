<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 12:08
 */


namespace GWTModule\Helper;

use App\Core\Request;
use App\IPixifiable;
use App\Pixie;
use GWTModule\IGWTService;
use MappedClass;

class SimpleRPCTargetResolverStrategy implements \RPCTargetResolverStrategy, IPixifiable
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var Request
     */
    protected $request;

    function __construct(Request $request)
    {
        $this->request = $request;
    }

    function resolveRPCTarget(MappedClass $interface)
    {
        $target = \GWTPHPContext::getInstance()->getClassLoader()->loadClass($interface->getMappedName().'Impl');
        $instance = $target->newInstance();
        $interfaces = class_implements($instance);

        if (in_array('App\\IPixifiable', $interfaces)) {
            $instance->setPixie($this->pixie);
        }

        if (in_array('GWTModule\\IGWTService', $interfaces)) {
            /** @var IGWTService $instance */
            $instance->setServlet($this->pixie->gwt->getServlet());
            $instance->setRequest($this->request);
        }

        return $instance;
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