<?php

namespace GWTModule;
use App\Core\Request;
use App\Pixie;

/**
 * GWT Module which sets up a GWTPHP gateway for the application.
 * @package    VulnInjection
 */
class GWTModule {

    /**
     * Pixie Dependency Container
     * @var Pixie
     */
    public $pixie;

    /**
     * @var boolean
     */
    protected $initialized = false;

    /**
     * @var RemoteServiceServlet
     */
    protected $servlet;

    public function __construct($pixie) {
        $this->pixie = $pixie;
    }

    protected function init()
    {
        if ($this->initialized) {
            return;
        }

        \Logger::configure($this->pixie->config->get('logger'));

        \GWTPHPContext::getInstance()->setServicesRootDir(realpath(dirname(__FILE__) . '/../../gwtphp-maps'));
        \GWTPHPContext::getInstance()->setGWTPHPRootDir(GWTPHP_DIR);

        $this->initialized = true;
    }

    public function getServlet()
    {
        if ($this->servlet) {
            return $this->servlet;
        }

        $this->init();

        $this->servlet = new RemoteServiceServlet($this->pixie);
        $mappedClassLoader = new \FolderMappedClassLoader();
        $this->servlet->setMappedClassLoader($mappedClassLoader);

        return $this->servlet;
    }
}
