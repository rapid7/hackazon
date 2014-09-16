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
 * analyses existing services. Warning: if 2 or more services have the same name, only one will appear in the returned data, 
 * as it is an associative array using the service name as key. 
 * @amfphpHide
 * @package Amfphp_Plugins_Discovery
 * @author Ariel Sommeria-Klein
 */
class AmfphpDiscoveryService {

    /**
     * @see AmfphpDiscovery
     * @var array of strings(patterns)
     */
    public static $excludePaths;

    /**
     * paths to folders containing services(relative or absolute). set by plugin.
     * @var array of paths
     */
    public static $serviceFolders;

    /**
     *
     * @var array of ClassFindInfo. set by plugin.
     */
    public static $serviceNames2ClassFindInfo;

    /**
     * restrict access to amfphp_admin. 
     * @var boolean
     */
    public static $restrictAccess;

    /**
     * get method roles
     * @param string $methodName
     * @return array
     */
    public function _getMethodRoles($methodName) {
        if (self::$restrictAccess) {
            return array('amfphp_admin');
        }
    }

    /**
     * finds classes in folder. If in subfolders add the relative path to the name.
     * recursive, so use with care.
     * @param string $rootPath
     * @param string $subFolder
     * @return array
     */
    protected function searchFolderForServices($rootPath, $subFolder) {
        $ret = array();
        $folderContent = scandir($rootPath . $subFolder);

        if ($folderContent) {
            foreach ($folderContent as $fileName) {
                //add all .php file names, but removing the .php suffix
                if (strpos($fileName, '.php')) {
                    $fullServiceName = $subFolder . substr($fileName, 0, strlen($fileName) - 4);
                    $ret[] = $fullServiceName;
                } else if ((substr($fileName, 0, 1) != '.') && is_dir($rootPath . $subFolder . $fileName)) {
                    $ret = array_merge($ret, $this->searchFolderForServices($rootPath, $subFolder . $fileName . '/'));
                }
            }
        }
        return $ret;
    }

    /**
     * returns a list of available services
     * @param array $serviceFolders
     * @param array $serviceNames2ClassFindInfo
     * @return array of service names
     */
    protected function getServiceNames(array $serviceFolders, array $serviceNames2ClassFindInfo) {
        $ret = array();
        foreach ($serviceFolders as $serviceFolderPath) {
            if(is_array($serviceFolderPath)){
                //case when using namespace
                $ret = array_merge($ret, $this->searchFolderForServices($serviceFolderPath[0], ''));
            }else{
                //case without namespace
                $ret = array_merge($ret, $this->searchFolderForServices($serviceFolderPath, ''));
            }
        }

        foreach ($serviceNames2ClassFindInfo as $key => $value) {
            $ret[] = $key;
        }

        return $ret;
    }

    /**
     * extracts 
     * - meta data from param tags:
     *     1) type is first word after tag name, name of the variable is second word ($ is removed)
     *     2) example is end of line after 'example: '
     * 
     * - return type
     * If data is missing because comment is incomplete the values are simply not set
     * @param string $comment 
     * @return array{'returns' => type, 'params' => array{variable name => parameter meta}}
     */
    protected function parseMethodComment($comment) {
        $exploded = explode('@', $comment);
        $ret = array();
        $params = array();
        foreach ($exploded as $tagLine) {
            if (strtolower(substr($tagLine, 0, 5)) == 'param') {
                //type
                $words = explode(' ', $tagLine);
                $type = trim($words[1]);
                $varName = trim(str_replace('$', '', $words[2]));
                $paramMeta = array();
                $paramMeta['type'] = $type;
                //example
                $example = '';
                $examplePos = strpos($tagLine, 'example:');
                if($examplePos !== false){
                    $example = substr($tagLine, $examplePos + 8);
                }
                $paramMeta['example'] = $example;
                $params[$varName] = $paramMeta;
                
            } else if (strtolower(substr($tagLine, 0, 6)) == 'return') {

                $words = explode(' ', $tagLine);
                $type = trim($words[1]);
                $ret['return'] = $type;
            }
        }
        $ret['param'] = $params;
        if (!isset($ret['return'])) {
            $ret['return'] = '';
        }
        return $ret;
    }

    /**
     * gets rid of blocks of 4 spaces and tabs, as well as comment markers. 
     * @param type $comment
     * @return type 
     */
    private function formatComment($comment){
        $ret = str_replace('    ', '', $comment);
        $ret = str_replace("\t", '', $ret);
        $ret = str_replace('/**', '', $ret);
        $ret = str_replace('*/', '', $ret);
        $ret = str_replace('*', '', $ret);
        return $ret;
    }
    /**
     * does the actual collection of data about available services
     * @return array of AmfphpDiscovery_ServiceInfo
     */
    public function discover() {
        $serviceNames = $this->getServiceNames(self::$serviceFolders, self::$serviceNames2ClassFindInfo);
        $ret = array();
        foreach ($serviceNames as $serviceName) {
            $serviceObject = Amfphp_Core_Common_ServiceRouter::getServiceObjectStatically($serviceName, self::$serviceFolders, self::$serviceNames2ClassFindInfo);
            $objR = new ReflectionObject($serviceObject);
            $objComment = $this->formatComment($objR->getDocComment());
            if (false !== strpos($objComment, '@amfphpHide')) {
                //methods including @amfHide should not appear in the back office but should still be accessible.
                continue;
            }            
            $methodRs = $objR->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array();
            foreach ($methodRs as $methodR) {
                $methodName = $methodR->name;

                if (substr($methodName, 0, 1) == '_') {
                    //methods starting with a '_' as they are reserved, so filter them out 
                    continue;
                }

                $parameters = array();
                $paramRs = $methodR->getParameters();

                $methodComment = $this->formatComment($methodR->getDocComment());
                if (false !== strpos($methodComment, '@amfphpHide')) {
                    //methods including @amfHide should not appear in the back office but should still be accessible.
                    continue;
                }
                $parsedMethodComment = $this->parseMethodComment($methodComment);
                foreach ($paramRs as $paramR) {

                    $parameterName = $paramR->name;
                    //get type from type hinting or from parsed method comment. type hinting has priority
                    $type = '';
                    //get example from parsed method comment only
                    $example = '';
                    
                    if (isset($parsedMethodComment['param'][$parameterName])) {
                        $paramMeta = $parsedMethodComment['param'][$parameterName]; 
                        if(isset($paramMeta['type'])){
                            $type = $paramMeta['type'];
                        }
                        if(isset($paramMeta['example'])){
                            $example = $paramMeta['example'];
                        }
                    }
                    try{
                        //this code will throw an exception saying that the class does not exist, only if the class is a namespace.
                        //in that case there's not much that can be done, so just ignore type.
                        if ($paramR->getClass()) {
                            $type = $paramR->getClass()->name;
                        }                         
                    }catch(Exception $e){
                    }

                    $parameterInfo = new AmfphpDiscovery_ParameterDescriptor($parameterName, $type, $example);

                    $parameters[] = $parameterInfo;
                }
                //get return from parsed return comment if exists
                $return = '';
                if(isset ($parsedMethodComment['return'])){
                    $return = $parsedMethodComment['return'];
                }
                $methods[$methodName] = new AmfphpDiscovery_MethodDescriptor($methodName, $parameters, $methodComment, $return);
            }

            $ret[$serviceName] = new AmfphpDiscovery_ServiceDescriptor($serviceName, $methods, $objComment);
        }
        //note : filtering must be done at the end, as for example excluding a Vo class needed by another creates issues
        foreach ($ret as $serviceName => $serviceObj) {
            foreach (self::$excludePaths as $excludePath) {
                if (strpos($serviceName, $excludePath) !== false) {
                    unset($ret[$serviceName]);
                    break;
                }
            }
        }
        return $ret;
    }

}

?>
