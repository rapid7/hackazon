<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 21.08.2014
 * Time: 12:40
 */


namespace App\Core;


/**
 * Links to be appended as response header Link.
 * @package App\Core
 */
class ResponseLink 
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var null|string
     */
    protected $rel;

    function __construct($url, $rel = null)
    {
        $this->rel = $rel;
        $this->url = $url;
    }

    /**
     * @return null|string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * @param null|string $rel
     */
    public function setRel($rel)
    {
        $this->rel = $rel;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
} 