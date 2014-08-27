<?php
use App\DataImport\CategoryProductImporter;

error_reporting(E_ERROR & ~E_NOTICE);
$root = dirname(__DIR__);
$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

if ($path = $_SERVER['argv'][1]) {
    $path = getcwd() . DIRECTORY_SEPARATOR . preg_replace('#^[\\\\/]+#', '', $path);

} else {
    $path = __DIR__ . '/../Hackazon';
}

echo "Your path is: $path \n";

if (!file_exists($path) || !is_dir($path)) {
    echo "Invalid directory.";
    exit;
}


$pixie = new \App\Pixie();
$pixie->bootstrap($root);
$result = CategoryProductImporter::create($pixie)->import($path);
var_dump($result);