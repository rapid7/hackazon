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
 * Filters are provided by Amfphp to allow your contexts to 'filter into' the rest of Amfphp, i.e. to call functions in your context at specific times<br />
 * Call addFilter to register a filter, with a default priority of 10, and call callFilter to actually execute the filter
 * 
 * The data structure is as follows: 
 * all registered filters : associative array ( filter name => name filters)
 * name filters : associative array containing filters for one filter name (priority => priority filters)
 * priority filters: numbered array containing filters for one filter name and one priority [callback1, callback2, etc.]
 * 
 * So for example if you were to call:
 * addFilter("FILTER_1", $obj, "method1");
 * addFilter("FILTER_1", $obj, "method2");
 * addFilter("FILTER_1", $obj, "method3", 15);
 * addFilter("FILTER_2", $obj, "method4");
 * 
 * the structure would be 
 * "FILTER_1" => array(
 *                      10 => [callback for method1, callback for method2]
 *                      15 => [callback for method3]
 * "FILTER_2" => array(
 *                      10 => [callback for method4]
 * 
 * This is a singleton, so use getInstance
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 *  */
class Amfphp_Core_FilterManager{
    /**
     * all registered filters
     */
    protected $allFilters = NULL;

    /**
    *protected instance of singleton
    */
    protected static $instance = NULL;
    /**
     * constructor
     */
    protected function __construct(){
        $this->allFilters = Array();
    }

    /**
     * get instance
     * @return Amfphp_Core_FilterManager
     */
    public static function getInstance() {
        if (self::$instance == NULL) {
            self::$instance = new Amfphp_Core_FilterManager();
        }
        return self::$instance;
    }

    /**
     * call the functions registered for the given filter. There can be as many parameters as necessary, but only the first
     * one can be changed and and returned by the callees.
     * The other parameters must be considered as context, and should not be modified by the callees, and will not be returned to the caller.
     * 
     * param 1: String $filterName the name of the filter which was used in addFilter( a string)
     * following params: parameters for the function call. As many as necessary can be passed, but only the first will be filtered
     * @return mixed the first call parameter, as filtered by the callees.
     */
    public function callFilters(){
        
        //get arguments with which to call the function. All except first, which is the filter name
        $filterArgs = func_get_args();
        $filterName = array_shift($filterArgs);
        $filtered = $filterArgs[0];
        if (isset($this->allFilters[$filterName])){
            //the filters matching name
            $nameFilters = &$this->allFilters[$filterName];
            //sort by priority
            ksort($nameFilters);
            // loop on filters matching filter name by priority
            foreach($nameFilters as $priorityFilters){
                //loop for each existing priority
                foreach($priorityFilters as $callBack){
                    $fromCallee = call_user_func_array($callBack, $filterArgs);
                    if($fromCallee !== null){ //!== null because otherwise array() doesn't qualify
                        $filtered = $filterArgs[0] = $fromCallee;
                    }
                    
                }
            }
        }

        return $filtered;
    }



    /**
     * register an object method for the given filter
     * call this method in your contexts to be notified when the filter occures
     * @see http://php.net/manual/en/function.call-user-func.php
     * @see http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback
     *
     * 
     * @param String $filterName  the name of the filter
     * @param Object $object the object on which to call the method
     * @param String $methodName the name of the method to call on the object.
     * @param int $priority.  0 is first, 10 is default, more is later
     */
    public function addFilter($filterName, $object, $methodName, $priority = 10){
        // init the filter placeholder
        if (!isset($this->allFilters[$filterName])){
            $this->allFilters[$filterName] = Array();
        }
        $nameFilters = &$this->allFilters[$filterName];
        if (!isset($nameFilters[$priority])){
            $nameFilters[$priority] = Array();
        }
        $priorityFilters = &$nameFilters[$priority];
        // add the filter callback
        $priorityFilters[] = array($object, $methodName);
    }
}
?>