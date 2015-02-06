<?php
namespace VulnModule\Config\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Route
{
    public $name;

    public $params = [];

    function __construct($options = [])
    {
        if ($options['name']) {
            $this->name = $options['name'];

        } else if (!is_array($options[0])) {
            $this->name = $options[0];

        } else if ($options[0] && !$options['params']) {
            $this->params = $options['params'];
        }

        if ($options['params']) {
            $this->params = is_array($options['params']) ? $options['params'] : [$options['params']];
        }
    }
}
