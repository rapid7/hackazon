<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 10:31
 */


namespace GWTModule;


use App\Pixie;

/**
 * Pixified version of
 * @package App\GWTPHP
 * @inheritdoc
 */
class RemoteServiceServlet extends \RemoteServiceServlet
{
    /**
     * @var \App\Pixie
     */
    protected $pixie;

    /**
     * @var PHPixieORMRepository
     */
    protected $repository;

    /**
     * @inheritdoc
     * @param Pixie $pixie
     */
    function __construct(Pixie $pixie)
    {
        parent::__construct();
        $this->pixie = $pixie;
        $this->repository = new PHPixieORMRepository($pixie);
    }

    /**
     * @return PHPixieORMRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @inheritdoc
     * @return Helper\SimpleRPCTargetResolverStrategy|\RPCTargetResolverStrategy
     */
    protected function getRPCTargetResolverStrategy()
    {
        $strategy = new Helper\SimpleRPCTargetResolverStrategy();
        $strategy->setPixie($this->pixie);
        return $strategy;
    }
} 