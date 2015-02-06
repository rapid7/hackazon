<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 16.01.2015
 * Time: 18:52
 */


namespace App\Templating;


use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Templating\Storage\Storage;
use Symfony\Component\Templating\Storage\StringStorage;

/**
 * Class PhpEngine
 * @package App\Templating
 */
class PhpEngine extends \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine
{
    protected $evalTemplateX;
    protected $evalParametersX;

    public function render($name, array $parameters = array())
    {
        $storage = $this->load($name);
        $key = hash('sha256', serialize($storage));
        $this->current = $key;
        $this->parents[$key] = null;

        // attach the global variables
        $parameters = array_replace($this->getGlobals(), $parameters);
        // render
        if (false === $content = $this->evaluateTpl($storage, $parameters)) {
            throw new \RuntimeException(sprintf('The template "%s" cannot be rendered.', $this->parser->parse($name)));
        }

        // decorator
        if ($this->parents[$key]) {
            /** @var SlotsHelper $slots */
            $slots = $this->get('slots');
            $this->stack[] = $slots->get('_content');
            $slots->set('_content', $content);

            $content = $this->render($this->parents[$key], $parameters);

            $slots->set('_content', array_pop($this->stack));
        }

        return $content;
    }

    protected function evaluateTpl(Storage $template, array $parameters = array())
    {

        $this->evalTemplateX = $template;
        $this->evalParametersX = $parameters;
        unset($template, $parameters);

        if (isset($this->evalParametersX['this'])) {
            throw new \InvalidArgumentException('Invalid parameter (this)');
        }
        if (isset($this->evalParametersX['view'])) {
            throw new \InvalidArgumentException('Invalid parameter (view)');
        }

        if ($this->evalTemplateX instanceof FileStorage) {
            extract($this->evalParametersX, EXTR_SKIP);
            $this->evalParametersX = null;

            ob_start();
            require $this->evalTemplateX;

            $this->evalTemplateX = null;

            return ob_get_clean();
        } elseif ($this->evalTemplateX instanceof StringStorage) {
            extract($this->evalParametersX, EXTR_SKIP);
            $this->evalParametersX = null;

            ob_start();
            eval('; ?>'.$this->evalTemplateX.'<?php ;');

            $this->evalTemplateX = null;

            return ob_get_clean();

        } else if ($this->evalTemplateX instanceof ClassStorage) {
            $callable = $this->evalTemplateX->getContent();

            if (!is_callable($callable)) {
                throw new \Exception("Template must be a valid callable");
            }

            $this->evalParametersX['view'] = $this;
            if ($this->has('form')) {
                $this->evalParametersX['formHelper'] = $this->get('form');
            }
            if ($this->has('translator')) {
                $this->evalParametersX['translatorHelper'] = $this['translator'];
            }

            $func = new \ReflectionMethod($callable[0], $callable[1]);
            $funcParams = $func->getParameters();
            $tplParams = [];
            foreach ($funcParams as $k => $param) {
                if (array_key_exists($param->getName(), $this->evalParametersX)) {
                    $tplParams[$k] = $this->evalParametersX[$param->getName()];
                } else {
                    $tplParams[$k] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                }
            }

            $this->evalParametersX = null;

            ob_start();
            call_user_func_array($callable, $tplParams);

            $this->evalTemplateX = null;

            return ob_get_clean();
        }

        return false;
    }

    protected function load($name)
    {

        $template = $this->parser->parse($name);

        $key = $template->getLogicalName();

        if (preg_match('/:(\w+)\.html\.php$/', $key, $matches)) {
            $key = $matches[1];
            $template = new TemplateReference(null, null, $key);
        }

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $storage = $this->loader->load($template);

        if (false === $storage) {
            throw new \InvalidArgumentException(sprintf('The template "%s" does not exist.', $template));
        }

        return $this->cache[$key] = $storage;
    }
}