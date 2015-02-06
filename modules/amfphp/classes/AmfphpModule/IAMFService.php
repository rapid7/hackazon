<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.01.2015
 * Time: 16:39
 */

namespace AmfphpModule;


use App\Pixie;
use VulnModule\Config\Context;

interface IAMFService {
    public function getPixie();
    public function setPixie(Pixie $pixie);
    public function getContext();
    public function setContext(Context $context);
}