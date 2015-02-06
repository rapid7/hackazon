<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.10.2014
 * Time: 19:29
 */

namespace GWTModule;


use App\Core\Request;
use VulnModule\Config\Context;

interface IGWTService {
    public function getServlet();
    public function setServlet(RemoteServiceServlet $servlet = null);
    public function getContext();
    public function setContext(Context $context = null);
    public function getRequest();
    public function setRequest(Request $request = null);
}