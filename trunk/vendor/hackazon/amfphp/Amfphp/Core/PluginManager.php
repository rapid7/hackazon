<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * Loads plugins for Amfphp. Plugins consist of a folder in the plugins folder. The folder and the class
 * should all have the same name. The file containing the class should be named with the class name with the '.php' suffix added.
 * It is the loaded class' responsability to load any  other resources that the plugin needs from the same folder.
 *  A plugin interacts with Amfphp by using the Amfphp_Core_FilterManager to register its functions
 * to be called at specific times with specific parameters during execution.
 * It's a singleton, so use getInstance
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-Klein
 */
class Amfphp_Core_PluginManager {

    /**
     * protected instance of singleton
     * @var Amfphp_Core_PluginManager
     *
     */
    protected static $instance = NULL;

    /**
     * plugin instances
     * @var array
     */
    protected $pluginInstances;

    /**
     * constructor
     */
    protected function __construct() {
        $this->pluginInstances = array();
    }

    /**
     * gives access to the singleton
     * @return Amfphp_Core_PluginManager
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Amfphp_Core_PluginManager();
        }
        return self::$instance;
    }

    /**
     * load the plugins
     * @param array $pluginFolders where to load the plugins from. Absolute paths. For example Amfphp/Plugins/
     * @param array $pluginsConfig  optional. an array containing the plugin configuration, using the plugin name as key.
     * @param array $sharedConfig optional. if both a specific config and a shared config are available, concatenate them to create the plugin config. 
     * Otherwise use whatever is not null
     * @param array $disabledPlugins  optional.  an array of names of plugins to disable
     */
    public function loadPlugins($pluginFolders, array $pluginsConfig = null, array $sharedConfig = null, array $disabledPlugins = null) {
        foreach ($pluginFolders as $pluginsFolderRootPath) {
            if (!is_dir($pluginsFolderRootPath)) {
                throw new Amfphp_Core_Exception('invalid path for loading plugins at ' . $pluginsFolderRootPath);
            }
            $folderContent = scandir($pluginsFolderRootPath);

            foreach ($folderContent as $pluginName) {
                if (!is_dir($pluginsFolderRootPath . '/' . $pluginName)) {
                    continue;
                }
                //avoid system folders
                if ($pluginName[0] == '.') {
                    continue;
                }

                //check first if plugin is disabled
                $shouldLoadPlugin = true;
                if ($disabledPlugins) {
                    foreach ($disabledPlugins as $disabledPlugin) {
                        if ($disabledPlugin == $pluginName) {
                            $shouldLoadPlugin = false;
                        }
                    }
                }
                if (!$shouldLoadPlugin) {
                    continue;
                }

                if (!class_exists($pluginName, false)) {
                    require_once $pluginsFolderRootPath . '/' . $pluginName . '/' . $pluginName . '.php';
                }

                $pluginConfig = array();
                if ($pluginsConfig && isset($pluginsConfig[$pluginName])) {
                    $pluginConfig = $pluginsConfig[$pluginName];
                }
                if ($sharedConfig) {
                    $pluginConfig += $sharedConfig;
                }


                $pluginInstance = new $pluginName($pluginConfig);
                $this->pluginInstances[] = $pluginInstance;
            }
        }
    }

}

?>
