<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 16:47
 */


namespace App\DependencyInjection;


use App\Helpers\PlainTemplateNameParser;
use App\Pixie;
use App\Templating\ClassTemplateLoader;
use App\Templating\PhpEngine;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Templating\TemplatingExtension;
use Symfony\Component\Translation\IdentityTranslator;
use VulnModule\Config\Context;
use VulnModule\Config\ContextMetadataFactory;
use VulnModule\Form\Extension\VulnExtension;

class Container extends Application
{
    /**
     * @var Pixie
     */
    protected $pixie;

    public function __construct(Pixie $pixie, array $values = array())
    {
        parent::__construct($values);
        $this->pixie = $pixie;

        $app = $this;

        $this['fs'] = function () {
            return new Filesystem();
        };

        $this['containerHelper'] = function () use ($app) {
            return $app->pixie->containerHelper;
        };


        $this['templating'] = function () use ($app) {
            //$loader = new FilesystemLoader([__DIR__ . '/../../../assets/views/%name%']);
            $loader = new ClassTemplateLoader('App\\Templating\\Templates');
            //$engine = new PhpEngine(new SimpleTemplateNameParser(__DIR__ . '/../../../assets/views'), $app['containerHelper'], $loader);
            $engine = new PhpEngine(new PlainTemplateNameParser(), $app['containerHelper'], $loader);

            $engine->setEscaper('html_strong', function ($value) use ($engine) {
                // Numbers and Boolean values get turned into strings which can cause problems
                // with type comparisons (e.g. === or is_int() etc).
                return is_string($value) ? htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $engine->getCharset(), true) : $value;
            });

            $engine->addHelpers([new TranslatorHelper($app['translator'])]);
            return $engine;
        };

        $this['translator'] = function () {
            return new IdentityTranslator();
        };

        $this->register(new ValidatorServiceProvider());
        $this->register(new FormServiceProvider());
        //$this->register(new TranslationServiceProvider());

        $this['form.extensions'] = $app->extend('form.extensions', function (array $extensions) use ($app) {
            $realPath = realpath(__DIR__ . '/../../../vendor/symfony/framework-bundle/Symfony/Bundle/FrameworkBundle/Resources/views/Form');
            $extensions[] = new TemplatingExtension($app['templating'], null, [
                $realPath ?: 'phar://' . __DIR__ . '/../../../vendor/symfony/framework-bundle.phar/Symfony/Bundle/FrameworkBundle/Resources/views/Form'
            ]);

            $extensions[] = new VulnExtension();

            return $extensions;
        });

        $this['annotation.reader'] = function () {
            $basePath = __DIR__ . '/../../../modules/vulninjection/classes/';
            AnnotationRegistry::registerAutoloadNamespace('VulnModule\\Config\\Annotation', $basePath);
            try {
                return new AnnotationReader();

            } catch (\Exception $e) {
                return null;
            }
        };

        $this['vulnerability.context_metadata_factory'] = function () use ($app) {
            $factory = new ContextMetadataFactory($app->pixie->annotationReader);
            $factory->addNamespace('App\\Controller\\', Context::TECH_GENERIC);
            $factory->addNamespace('App\\Controller\\', Context::TECH_WEB);
            $factory->addNamespace('AmfphpModule\\Services\\', Context::TECH_AMF);
            $factory->addNamespace('App\\Rest\\Controller\\', Context::TECH_REST);
            $factory->addNamespace('', Context::TECH_GWT);
            return $factory;
        };
    }
}