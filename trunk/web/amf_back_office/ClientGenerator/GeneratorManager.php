<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp__BackOffice_ClientGenerator
 * 
 */

 /**
 * loads the generators
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp__BackOffice_ClientGenerator
 */
class Amfphp_BackOffice_ClientGenerator_GeneratorManager {
    /**
     * load generators
     * @param array of strings $generatorFolders 
     */
    public function loadGenerators($generatorFolders) {
        $ret = array();
        foreach ($generatorFolders as $generatorFolderRootPath) {
            if (!is_dir($generatorFolderRootPath)) {
                throw new Amfphp_Core_Exception('invalid path for loading generator at ' . $generatorFolderRootPath);
            }
            $folderContent = scandir($generatorFolderRootPath);

            foreach ($folderContent as $generatorName) {
                if (!is_dir($generatorFolderRootPath . '/' . $generatorName)) {
                    continue;
                }
                //avoid system folders
                if ($generatorName[0] == '.') {
                    continue;
                }

                if (!class_exists($generatorName, false)) {
                    require_once $generatorFolderRootPath . '/' . $generatorName . '/' . $generatorName . '.php';
                }

                $generatorInstance = new $generatorName();
                $ret[$generatorName] = $generatorInstance;
            }
        }
        return $ret;
    }
}

?>
