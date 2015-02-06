<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 26.11.2014
 * Time: 16:26
 *
 * The PHP has a problem with compressing more than 2042 files in one PHAR:
 * @link[https://bugs.php.net/bug.php?id=53467]
 * Here we avoid putting too much files into one phar
 */

$fileName = __DIR__ . '/vendor.phar';
$tmpFileName = $fileName . '.tmp';
$vendorDir = __DIR__ . DIRECTORY_SEPARATOR . 'vendor';

if (file_exists($tmpFileName)) {
    unlink($tmpFileName);
}

$phars = [];


//$phar->buildFromIterator(
//    new RecursiveIteratorIterator(
//        new RecursiveDirectoryIterator($vendorDir)),
//    $vendorDir);

foreach (new DirectoryIterator($vendorDir) as $dir) {
    if (in_array($dir->getFilename(), ['..', '.', 'composer']) || !$dir->isDir()) {
        continue;
    }

    foreach (new DirectoryIterator($dir->getRealPath()) as $subDir) {
        if (in_array($subDir->getFilename(), ['..', '.']) || !$subDir->isDir()) {
            continue;
        }

        $fName = $subDir->getRealPath() . '.phar';
        $fNameTmp = $fName . '.tmp';
        //var_dump([$fName, $fNameTmp, $subDir->getRealPath()]);exit;
        $phar = new Phar($fNameTmp);

        $phar->buildFromDirectory($subDir->getRealPath(), '#\\.(?!git)#');

        if (Phar::canCompress(Phar::GZ)) {
            $phar->compressFiles(Phar::GZ);

        } else if (Phar::canCompress(Phar::BZ2)) {
            $phar->compressFiles(Phar::BZ2);
        }

        if (file_exists($fName)) {
            unlink($fName);
        }

        unset($phar);

        rename($fNameTmp, $fName);
        //delDirTree($subDir->getRealPath());
        $phars[$dir->getFilename() . '/' . $subDir->getFilename()] =
            str_replace(DIRECTORY_SEPARATOR, '/', str_replace($vendorDir, '', $fName));
    }
}

$autoloadFiles = [
    'composer/autoload_classmap.php',
    'composer/autoload_files.php',
    'composer/autoload_namespaces.php',
    'composer/autoload_psr4.php',
];

foreach ($autoloadFiles as $file) {
    $filePath = __DIR__.'/vendor/'.$file;
    $content = file_get_contents($filePath);
    $content = preg_replace('#(?<!\'phar://\' . )\\$vendorDir\\s+.\\s+\'(/[-\\w\\d_]+/[-\\w\\d_]+)#',
        '\'phar://\' . $vendorDir . \'$1.phar', $content);
    if ($content) {
        file_put_contents($filePath, $content);
    }
}



function delDirTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file") && !is_link($dir)) ? delDirTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}