<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.01.2015
 * Time: 19:10
 */


namespace App\Templating;


use Symfony\Component\Templating\Storage\Storage;

class ClassStorage extends Storage
{

    /**
     * Returns the content of the template.
     *
     * @return string The template content
     *
     * @api
     */
    public function getContent()
    {
        return $this->template;
    }
}