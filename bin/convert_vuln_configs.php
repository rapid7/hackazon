<?php
use VulnModule\Config\Context;
use VulnModule\Vulnerability as V;

error_reporting(E_ERROR & ~E_NOTICE);
ini_set("display_errors", 1);
$root = dirname(__DIR__);

require $root.'/vendor/autoload.php';

$pixie = new \App\Pixie();
$pixie->bootstrap($root);

$sourceDir = $root.'/assets/config/vulninjection';
$targetDir = $root.'/assets/config/vuln.sample';

$reader = new \VulnModule\Storage\Version1Reader($sourceDir);

$contextTypes = [
    'account' => Context::TYPE_CONTROLLER,
    'amf' => Context::TYPE_APPLICATION,
    'cart' => Context::TYPE_CONTROLLER,
    'checkout' => Context::TYPE_CONTROLLER,
    'contact' => Context::TYPE_CONTROLLER,
    'default' => Context::TYPE_APPLICATION,
    'faq' => Context::TYPE_CONTROLLER,
    'product' => Context::TYPE_CONTROLLER,
    'rest' => Context::TYPE_APPLICATION,
    'review' => Context::TYPE_CONTROLLER,
    'search' => Context::TYPE_CONTROLLER,
    'user' => Context::TYPE_CONTROLLER,
    'wishlist' => Context::TYPE_CONTROLLER,
];

$writer = new \VulnModule\Storage\PHPFileWriter($pixie, $targetDir);

foreach (new \DirectoryIterator($sourceDir) as $fileInfo) {
    if ($fileInfo->isDot() || $fileInfo->getExtension() != 'php') {
        continue;
    }

    $configName = $fileInfo->getBasename('.php');
    $context = $reader->read($configName);
    $context->setType(isset($contextTypes[$configName]) ? $contextTypes[$configName] : Context::TYPE_APPLICATION);
    $writer->write($context);
}

