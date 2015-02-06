<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 12:43
 */


namespace App\Utils;

use Composer\Script\Event;

/**
 * Composer scripts to create phared vendors
 * @package App\Utils
 */
class ComposerPharMaker 
{
    public static function convertVendorsToPhar(Event $event)
    {
        $vendorDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . 'vendor';
        $phars = [];

        echo "Converting vendor package dirs to phar...\n";

        foreach (new \DirectoryIterator($vendorDir) as $dir) {
            if (in_array($dir->getFilename(), ['..', '.', 'composer']) || !$dir->isDir()) {
                continue;
            }

            foreach (new \DirectoryIterator($dir->getRealPath()) as $subDir) {
                if (in_array($subDir->getFilename(), ['..', '.']) || !$subDir->isDir()) {
                    continue;
                }

                echo "... " . $dir->getFilename() . '/' . $subDir->getFilename() . "\n";

                $fName = $subDir->getRealPath() . '.phar';
                $fNameTmp = $fName . '.tmp';
                $phar = new \Phar($fNameTmp);

                $phar->buildFromDirectory($subDir->getRealPath(), '#\\.(?!git)#');

                if (\Phar::canCompress(\Phar::GZ)) {
                    $phar->compressFiles(\Phar::GZ);

                } else if (\Phar::canCompress(\Phar::BZ2)) {
                    $phar->compressFiles(\Phar::BZ2);
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

        echo "\nConverting autoload files: \n";

        $autoloadFiles = [
            'composer/autoload_classmap.php',
            'composer/autoload_files.php',
            'composer/autoload_namespaces.php',
            'composer/autoload_psr4.php',
        ];

        foreach ($autoloadFiles as $file) {
            echo $file . "\n";
            $filePath = $vendorDir . DIRECTORY_SEPARATOR . $file;
            $content = file_get_contents($filePath);
            $content = preg_replace('#(?<!\'phar://\' . )\\$vendorDir\\s+.\\s+\'(/[-\\w\\d_]+/[-\\w\\d_]+)#',
                '\'phar://\' . $vendorDir . \'$1.phar', $content);
            if ($content) {
                file_put_contents($filePath, $content);
            }
        }

        echo "\nComplete!\n";
    }

    public static function removeVendorPackagesDirs()
    {
        $vendorDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'vendor';

        echo "Removing vendor package dirs ...\n";

        foreach (new \DirectoryIterator($vendorDir) as $dir) {
            if (in_array($dir->getFilename(), ['..', '.', 'composer']) || !$dir->isDir()) {
                continue;
            }

            foreach (new \DirectoryIterator($dir->getRealPath()) as $subDir) {
                if (in_array($subDir->getFilename(), ['..', '.']) || !$subDir->isDir()) {
                    continue;
                }

                echo "... " . $dir->getFilename() . '/' . $subDir->getFilename();
                if (!self::delDirTree($subDir->getRealPath())) {
                    echo " ...  cannot remove. Please remove manually.";
                }

                echo "\n";
            }
        }

        echo "\nComplete!\n";
    }

    public static function postPackageInstall(Event $event)
    {
        //self::convertVendorsToPhar($event);
        //var_dump($event);
        //$event->getComposer()->getInstallationManager()
    }

    /**
     * @param $dir
     * @return bool
     */
    public static function delDirTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            try {
                if (is_dir("$dir/$file") && !is_link($dir)) {
                    self::delDirTree("$dir/$file");

                } else {
                    chmod("$dir/$file", 644);
                    unlink("$dir/$file");
                }

            } catch (\ErrorException $e) {
                echo "$dir/$file \n";
            }
        }
        try {
            return rmdir($dir);
        } catch (\ErrorException $e) {
            return false;
        }
    }
}