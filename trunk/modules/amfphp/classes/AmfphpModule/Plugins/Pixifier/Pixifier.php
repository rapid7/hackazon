<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 10.10.2014
 * Time: 15:45
 */
use App\Pixie;

/**
 * Adds Pixie and other stuff to service object.
 * @package AmfphpModule\Plugins\Pixifier
 */
class Pixifier
{
    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(array $config = null)
    {
        $filterManager = \Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(\Amfphp_Core_Common_ServiceRouter::FILTER_SERVICE_OBJECT, $this, 'filterServiceObject');
        $this->pixie = $config['pixie'];
    }

    public function filterServiceObject($serviceObject/*, $serviceName, $methodName*/) {
        if (method_exists($serviceObject, 'setPixie')) {
            $serviceObject->setPixie($this->pixie);
        }

        if (method_exists($serviceObject, 'setContext')) {
            $serviceObject->setContext($this->pixie->vulnService->getConfig()->getCurrentContext());
        }
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