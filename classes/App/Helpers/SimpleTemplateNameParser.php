<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov
 * Date: 17.02.14
 * Time: 22:06
 */

namespace App\Helpers;


use Symfony\Component\Templating\TemplateReference;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class SimpleTemplateNameParser implements TemplateNameParserInterface
{
    private $root;

    public function __construct($root)
    {
        $this->root = $root;
    }

    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        $engine = null;
        if (false !== strpos($name, ':')) {
            $path = preg_replace('#:(?![\\\\\\/])#', '/', $name);
        } else {
            $path = $this->root . '/' . $name;
        }

        return new TemplateReference($path, 'php');
    }
}