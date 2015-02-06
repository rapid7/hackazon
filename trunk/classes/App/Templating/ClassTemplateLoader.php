<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.01.2015
 * Time: 19:22
 */


namespace App\Templating;


use Symfony\Component\Templating\Loader\LoaderInterface;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\TemplateReferenceInterface;

class ClassTemplateLoader implements LoaderInterface
{
    protected $className;

    protected $templates;

    function __construct($className)
    {
        $this->className = $className;
        $this->templates = new $className;
    }

    /**
     * Loads a template.
     *
     * @param TemplateReferenceInterface $template A template
     *
     * @return Storage|bool false if the template cannot be loaded, a Storage instance otherwise
     *
     * @api
     */
    public function load(TemplateReferenceInterface $template)
    {
        $method = $template->get('name');
        if (!method_exists($this->templates, $method)) {
            return false;
        }

        return new ClassStorage([$this->templates, $method]);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param TemplateReferenceInterface $template A template
     * @param int $time The last modification time of the cached template (timestamp)
     *
     * @return bool
     *
     * @api
     */
    public function isFresh(TemplateReferenceInterface $template, $time)
    {
        if (false === $storage = $this->load($template)) {
            return false;
        }

        return true;
    }
}