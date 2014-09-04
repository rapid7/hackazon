<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 04.09.2014
 * Time: 12:24
 */


namespace GWTModule;


trait Servletable
{
    /**
     * @var RemoteServiceServlet
     */
    protected $servlet;

    /**
     * @return RemoteServiceServlet
     */
    public function getServlet()
    {
        return $this->servlet;
    }

    /**
     * @param RemoteServiceServlet $servlet
     */
    public function setServlet($servlet)
    {
        $this->servlet = $servlet;
    }
}