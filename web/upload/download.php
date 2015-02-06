<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 11.09.2014
 * Time: 17:05
 */

error_reporting(E_ERROR & ~E_NOTICE);
$root = dirname(__DIR__).'/..';
$loader = require $root.'/vendor/autoload.php';
$loader->add('', $root.'/classes/');

$pixie = new \App\Pixie();
$pixie->bootstrap($root);

if (!$pixie->getParameter('parameters.use_external_dir')) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$fileName = $_GET['image'];
/** @var \App\Model\File $file */
if (is_numeric($fileName)) {
    $file = $pixie->orm->get('file', $fileName);
} else {
    $file = $pixie->orm->get('file')->where('path', 'LIKE',  '%'.$fileName)->find();
}

if (!$file->loaded() || !file_exists($file->path) || !is_file($file->path)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}


$filePath = $file->path;
$imgData = getimagesize($filePath);
header('Content-Type:'.$imgData['mime']);
header('Content-Length: ' . filesize($filePath));
readfile($filePath);