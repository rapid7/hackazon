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

class PlainTemplateNameParser implements TemplateNameParserInterface
{

    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        }

        return new TemplateReference($name, 'php');
    }
}