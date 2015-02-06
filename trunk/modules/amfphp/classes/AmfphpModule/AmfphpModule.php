<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.09.2014
 * Time: 20:05
 */
namespace AmfphpModule;


use AmfphpModule\Core\HttpRequestGatewayFactory;
use App\Pixie;
use AmfphpModule\Core\Config;

/**
 * Class AmfphpModule
 */
class AmfphpModule 
{
    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    public function run()
    {
        $config = new Config($this->pixie);
        $gateway = HttpRequestGatewayFactory::createGatewayEx($this->pixie, $config);
        $gateway->service();
        $gateway->output();
    }
} 