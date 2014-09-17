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
 * utils for Amf handling
 *
 * @package Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Util {

    /**
     * looks if the system is Big Endain or not
     * @return <Boolean>
     */
    static public function isSystemBigEndian() {
        $tmp = pack('d', 1); // determine the multi-byte ordering of this machine temporarily pack 1
        return ($tmp == "\0\0\0\0\0\0\360\77");
    }

    /**
     * applies a function to all objects contained by $obj and $obj itself.
     * iterates on $obj and its sub objects, which can iether be arrays or objects
     * @param mixed $obj the object/array that will be iterated on
     * @param array $callBack the function to apply to obj and subobjs. must take 1 parameter, and return the modified object
     * @param int $recursionDepth current recursion depth. The first call should be made with this set 0. default is 0
     * @param int $maxRecursionDepth default is 30
     * @param bool $ignoreAmfTypes ignore objects with type in Amfphp_Core_Amf_Types package. could maybe be replaced by a regexp, but this is better for performance
     * @return mixed array or object, depending on type of $obj
     */
    static public function applyFunctionToContainedObjects($obj, $callBack, $recursionDepth = 0, $maxRecursionDepth = 30, $ignoreAmfTypes = true) {
        if ($recursionDepth == $maxRecursionDepth) {
            throw new Amfphp_Core_Exception("couldn't recurse deeper on object. Probably a looped reference");
        }
        //don't apply to Amfphp types such as byte array
        if ($ignoreAmfTypes && is_object($obj) && substr(get_class($obj), 0, 21) == 'Amfphp_Core_Amf_Types') {
            return $obj;
        }

        //apply callBack to obj itself
        $obj = call_user_func($callBack, $obj);

        //if $obj isn't a complex type don't go any further
        if (!is_array($obj) && !is_object($obj)) {
            return $obj;
        }

        foreach ($obj as $key => $data) { // loop over each element
            $modifiedData = null;
            if (is_object($data) || is_array($data)) {
                //data is complex, so don't apply callback directly, but recurse on it
                $modifiedData = self::applyFunctionToContainedObjects($data, $callBack, $recursionDepth + 1, $maxRecursionDepth);
            } else {
                //data is simple, so apply data
                $modifiedData = call_user_func($callBack, $data);
            }
            //store converted data
            if (is_array($obj)) {
                $obj[$key] = $modifiedData;
            } else {
                $obj->$key = $modifiedData;
            }
        }

        return $obj;
    }


}

?>
