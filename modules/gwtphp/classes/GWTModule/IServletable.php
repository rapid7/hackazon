<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.10.2014
 * Time: 19:29
 */

namespace GWTModule;


interface IServletable {
    function getServlet();
    function setServlet(RemoteServiceServlet $servlet = null);
} 