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
 * This is a test/example service. Remove it for production use
 *
 * @package Amfphp_Services
 * @author Ariel Sommeria-klein
 */
class ExampleService {


    /**
     * return one param
     * @param mixed $param example: {"_explicitType":"myType", "intVal":2, "stringVal":"bla", "arrayVal":[1,2, "ert"]}
     * @return mixed 
     */
    public function returnOneParam($param) {
        return $param;
    }

    /**
     * return sum
     * @param int $number1 example: 2
     * @param int $number2 example: 3
     * @return int 
     */
    public function returnSum($number1, $number2) {
        return $number1 + $number2;
    }

    /**
     * return null
     * @return null 
     */
    public function returnNull() {
        return null;
    }

    /**
     * return bla
     * @return String 
     */
    public function returnBla() {
        return 'bla';
    }

    /**
     * throy exception
     * @param string $arg1
     * @throws Exception
     */
    public function throwException($arg1) {
        throw new Exception("test exception $arg1", 123);
    }

    /**
     * return after one second
     * @return String 
     */
    public function returnAfterOneSecond() {
        sleep(1);
        return 'slept for 1 second';
    }

    /**
     * return test header
     * @return mixed
     */
    public function returnTestHeader() {
        $header = Amfphp_Core_Amf_Handler::$requestPacket->headers[0];
        return $header->data;
    }

    /**
     * shouldn't appear in the service browser or be available as a service
     */
    public function _reservedMethod() {
        
    }

    /**
     * return array
     * @return array 
     */
    public function returnArray() {
        return array(0, 1 => 2, 3 => 4, 5 => array(6 => 7));
    }

    /**
     * return opposite
     * @param boolean $value
     * @return boolean 
     */
    public function returnOpposite($value) {
        return!$value;
    }

    /**
     * return bitwise and
     * @param boolean $value1
     * @param boolean $value2
     * @return boolean 
     */
    public function returnBitwiseAnd($value1, $value2) {
        return ($value1 && $value2);
    }

    /**
     * static return one param
     * @param mixed $param
     * @return mixed
     */
    public static function staticReturnOneParam($param) {
        return $param;
    }

    /**
     * use to test for serialization performance. Each item contains a random int, float, and string
     * @param int $numItems example: 1000
     * @return array 
     */
    public function returnLargeDataSet($numItems) {
        $ret = array();
        for ($i = 0; $i < $numItems; $i++) {
            $item = new stdClass();
            $item->int = rand(-1000, 1000);
            $item->float = rand(-1000, 1000) / 100;
            $item->string = md5(rand(-1000, 1000));
            $ret[] = $item;
           
        }
        return $ret;
    }

    /**
     * use to test Vo conversion performance. Each item contains a random int, float, and string, and is typed
     * @param int $numItems example: 1000
     * @return array 
     */
    public function returnLargeTypedDataSet($numItems) {
        $ret = array();
        for ($i = 0; $i < $numItems; $i++) {
            $item = new DummyVo();
            $item->int = rand(-1000, 1000);
            $item->float = rand(-1000, 1000) / 100;
            $item->string = md5(rand(-1000, 1000));
            $ret[] = $item;
        }
        return $ret;
    }    
    
    /**
     * dummy function to see how the backoffice tools react when there are many parameters.
     * @param type $a
     * @param type $b
     * @param type $c
     * @param type $d
     * @param type $e
     * @param type $f 
     */
    public function manyParams($a, $b, $c, $d, $e, $f){
        
    }
    
    /**
     * simply to see if this doesn't appear in the back office, but is still callable
     * @amfphpHide
     */
    public function testAmfphpHide(){
        return "bla";
    }
    
/**
 * receives an array(flex array collections are deserialized to arrays), and sends back an array collection.
*/
  public function testArrayCollection(array $data){
        $ret = new stdClass();
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $ret->$explicitTypeField = "flex.messaging.io.ArrayCollection";
        $ret->source = $data;
        return $ret;
    }
	
    /**
     * useful for testing a messy, nested typed obj
    * @param mixed $param example:  {"_explicitType":"UserVo1", "name":"ariel", "status":"bla", "sub1":  {"_explicitType":"Sub1", "name":"ariel", "status":"bla", "sub2":  {"_explicitType":"Sub2", "name":"ariel", "status":"bla"}, "sub2again": {"_explicitType":"Sub2", "name":"ariel2", "status":"bla2"}}}
    */
    public function testComplicatedTypedObj($param){
        return $param;
    }
    
    /**
     * adds custom markers for monitoring
     */
    public function testCustomMonitorTime(){
        usleep(200000);
        AmfphpMonitor::addTime('operation 1');
        usleep(200000);
        AmfphpMonitor::addTime('operation 2');
        usleep(200000);
        AmfphpMonitor::addTime('operation 3');
        return 'bla';
    }
}

/**
 * dummy class
 */
class DummyVo {}
?>