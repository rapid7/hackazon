<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 12:08
 */


namespace GWTModule\Helper;


use App\Traits\Pixifiable;
use MappedClass;

class SimpleRPCTargetResolverStrategy implements \RPCTargetResolverStrategy
{
    use Pixifiable;

    function resolveRPCTarget(MappedClass $interface)
    {
        $target = \GWTPHPContext::getInstance()->getClassLoader()->loadClass($interface->getMappedName().'Impl');
        $instance = $target->newInstance();

        if (in_array('App\\Traits\\Pixifiable', $target->getTraitNames())) {
            $instance->setPixie($this->pixie);
        }

        if (in_array('GWTModule\\Servletable', $target->getTraitNames())) {
            $instance->setServlet($this->pixie->gwt->getServlet());
        }

        return $instance;
    }
} 