<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.01.2015
 * Time: 16:41
 */


namespace AmfphpModule;


use App\Pixie;
use VulnModule\Config\Context;
use VulnModule\Config\FieldDescriptor;

/**
 * Base class for services, which accepts a pixie instance and the current vulnerability context.
 * @package AmfphpModule
 */
class Service implements IAMFService
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @return Pixie
     * @amfphpHide
     */
    public function getPixie()
    {
        return $this->pixie;
    }

    /**
     * @param Pixie $pixie
     * @amfphpHide
     */
    public function setPixie(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * @return Context
     * @amfphpHide
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Context $context
     * @amfphpHide
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param $key
     * @param $rawValue
     * @return \VulnModule\VulnerableField
     * @amfphpHide
     */
    public function wrap($key, $rawValue)
    {
        return $this->pixie->vulnService->wrapValue($key, $rawValue, FieldDescriptor::SOURCE_BODY);
    }
}