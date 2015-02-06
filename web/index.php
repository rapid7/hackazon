<?php
error_reporting(E_ERROR & ~E_NOTICE);
ini_set("display_errors", 1);
$root = dirname(__DIR__);

$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

$pixie = new \App\Pixie();
$pixie->bootstrap($root)->handle_http_request();

