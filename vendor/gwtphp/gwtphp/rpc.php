<?php
require_once dirname(__FILE__) . "/../vendor/autoload.php";

Logger::configure(array(
    'rootLogger' => array(
        'appenders' => array('default'),
    ),
    'appenders' => array(
        'default' => array(
            'class' => 'LoggerAppenderNull',
        ),
		// uncomment to enable logging
        /*'default' => array(
            'class' => 'LoggerAppenderFile',
            'layout' => array(
                'class' => 'LoggerLayoutHtml'
            ),
            'params' => array(
            	'file' => 'log.html',
            	'append' => true
            )
        ),*/
    )
));

GWTPHPContext::getInstance()->setServicesRootDir(dirname(__FILE__).'/gwtphp-maps');
GWTPHPContext::getInstance()->setGWTPHPRootDir(GWTPHP_DIR);

$servlet = new RemoteServiceServlet();

$mappedClassLoader = new FolderMappedClassLoader();

$servlet->setMappedClassLoader($mappedClassLoader);
$servlet->start();

?>