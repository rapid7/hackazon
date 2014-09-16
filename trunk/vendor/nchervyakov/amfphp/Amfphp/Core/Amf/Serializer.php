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
 * AmfSerializer manages the job of translating PHP objects into
 * the actionscript equivalent via Amf.  The main method of the serializer
 * is the serialize method which takes and AmfObject as it's argument
 * and builds the resulting Amf Message.
 *
 * @package Amfphp_Core_Amf
 */
class Amfphp_Core_Amf_Serializer implements Amfphp_Core_Common_ISerializer {

    /**
     *
     * @var String the output stream
     */
    protected $outBuffer;

    /**
     * packet
     * @var Amfphp_Core_Amf_Packet
     */
    protected $packet;

    /**
     * the maximum amount of objects stored for reference
     */
    const MAX_STORED_OBJECTS = 1024;

    /**
     *
     * used for Amf0 references
     * @var array
     */
    protected $Amf0StoredObjects;

    /**
     *
     * used for Amf3 references
     * @var array
     */
    protected $storedObjects;

    /**
     * amf3 references to strings
     * @var array
     */
    protected $storedStrings;

    /**
     * used for traits references. key: class name. value: array(reference id, array(property names))
     * @var array
     */
    protected $className2TraitsInfo;

    /**
     * converts VOs directly if set, rather than instanciating anonymous classes that are converted later
     * @var Amfphp_Core_Common_IVoConverter
     */
    public $voConverter;

    /**
     * converts from php object to binary
     * @param Amfphp_Core_Amf_Packet $data
     */
    public function serialize($data) {
        $this->packet = $data;
        $this->resetReferences();

        $this->writeInt(0); //  write the version (always 0)
        $count = count($this->packet->headers);
        $this->writeInt($count); // write header count
        for ($i = 0; $i < $count; $i++) {
            $this->resetReferences();
            //write header
            $header = $this->packet->headers[$i];
            $this->writeUTF($header->name);
            if ($header->required) {
                $this->writeByte(1);
            } else {
                $this->writeByte(0);
            }
            $tempBuf = $this->outBuffer;
            $this->outBuffer = '';

            $this->writeData($header->data);
            $serializedHeader = $this->outBuffer;
            $this->outBuffer = $tempBuf;
            $this->writeLong(strlen($serializedHeader));
            $this->outBuffer .= $serializedHeader;
        }
        $count = count($this->packet->messages);
        $this->writeInt($count); // write the Message  count
        for ($i = 0; $i < $count; $i++) {
            $this->resetReferences();
            //write body.
            $message = $this->packet->messages[$i];
            $this->writeUTF($message->targetUri);
            $this->writeUTF($message->responseUri);
            //save the current buffer, and flush it to write the Message
            $tempBuf = $this->outBuffer;
            $this->outBuffer = '';
            $this->writeData($message->data);
            $serializedMessage = $this->outBuffer;
            $this->outBuffer = $tempBuf;
            $this->writeLong(strlen($serializedMessage));
            $this->outBuffer .= $serializedMessage;
        }

        return $this->outBuffer;
    }

    /**
     * initialize reference arrays and counters. Call before writing a body or a header, as the indices are local to each message body or header
     */
    protected function resetReferences() {
        $this->Amf0StoredObjects = array();
        $this->storedStrings = array();
        $this->storedObjects = array();
        $this->className2TraitsInfo = array();
    }

    /**
     * get serialized data output
     * @return string
     */
    public function getOutput() {
        return $this->outBuffer;
    }

    /**
     * writeByte writes a singe byte to the output stream
     * 0-255 range
     *
     * @param int $b An int that can be converted to a byte
     */
    protected function writeByte($b) {
        $this->outBuffer .= pack('c', $b); // use pack with the c flag
    }

    /**
     * writeInt takes an int and writes it as 2 bytes to the output stream
     * 0-65535 range
     *
     * @param int $n An integer to convert to a 2 byte binary string
     */
    protected function writeInt($n) {
        $this->outBuffer .= pack('n', $n); // use pack with the n flag
    }

    /**
     * writeLong takes an int, float or double and converts it to a 4 byte binary string and
     * adds it to the output buffer
     *
     * @param long $l A long to convert to a 4 byte binary string
     */
    protected function writeLong($l) {
        $this->outBuffer .= pack('N', $l); // use pack with the N flag
    }

    /**
     * writeDouble takes a float as the input and writes it to the output stream.
     * Then if the system is big-endian, it reverses the bytes order because all
     * doubles passed via remoting are passed little-endian.
     *
     * @param double $d The double to add to the output buffer
     */
    protected function writeDouble($d) {
        $b = pack('d', $d); // pack the bytes
        if (Amfphp_Core_Amf_Util::isSystemBigEndian()) { // if we are a big-endian processor
            $r = strrev($b);
        } else { // add the bytes to the output
            $r = $b;
        }

        $this->outBuffer .= $r;
    }

    /**
     * writeUTF takes and input string, writes the length as an int and then
     * appends the string to the output buffer
     *
     * @param string $s The string less than 65535 characters to add to the stream
     */
    protected function writeUtf($s) {
        $this->writeInt(strlen($s)); // write the string length - max 65535
        $this->outBuffer .= $s; // write the string chars
    }

    /**
     * writeLongUTF will write a string longer than 65535 characters.
     * It works exactly as writeUTF does except uses a long for the length
     * flag.
     *
     * @param string $s A string to add to the byte stream
     */
    protected function writeLongUtf($s) {
        $this->writeLong(strlen($s));
        $this->outBuffer .= $s; // write the string chars
    }

    /**
     * writeBoolean writes the boolean code (0x01) and the data to the output stream
     *
     * @param bool $d The boolean value
     */
    protected function writeBoolean($d) {
        $this->writeByte(1); // write the 'boolean-marker'
        $this->writeByte($d); // write the boolean byte (0 = FALSE; rest = TRUE)
    }

    /**
     * writeString writes the string code (0x02) and the UTF8 encoded
     * string to the output stream.
     * Note: strings are truncated to 64k max length. Use XML as type
     * to send longer strings
     *
     * @param string $d The string data
     */
    protected function writeString($d) {
        $count = strlen($d);
        if ($count < 65536) {
            $this->writeByte(2);
            $this->writeUTF($d);
        } else {
            $this->writeByte(12);
            $this->writeLongUTF($d);
        }
    }

    /**
     * writeXML writes the xml code (0x0F) and the XML string to the output stream
     * Note: strips whitespace
     * @param Amfphp_Core_Amf_Types_Xml $d
     */
    protected function writeXML(Amfphp_Core_Amf_Types_Xml $d) {
        if (!$this->handleReference($d->data, $this->Amf0StoredObjects)) {
            $this->writeByte(0x0F);
            $this->writeLongUTF(preg_replace('/\>(\n|\r|\r\n| |\t)*\</', '><', trim($d->data)));
        }
    }

    /**
     * writeDate writes the date code (0x0B) and the date value (milliseconds from 1 January 1970) to the output stream, along with an empty unsupported timezone
     *
     * @param Amfphp_Core_Amf_Types_Date $d The date value
     */
    protected function writeDate(Amfphp_Core_Amf_Types_Date $d) {
        $this->writeByte(0x0B);
        $this->writeDouble($d->timeStamp);
        $this->writeInt(0);
    }

    /**
     * writeNumber writes the number code (0x00) and the numeric data to the output stream
     * All numbers passed through remoting are floats.
     *
     * @param int $d The numeric data
     */
    protected function writeNumber($d) {
        $this->writeByte(0); // write the number code
        $this->writeDouble(floatval($d)); // write  the number as a double
    }

    /**
     * writeNull writes the null code (0x05) to the output stream
     */
    protected function writeNull() {
        $this->writeByte(5); // null is only a  0x05 flag
    }

    /**
     * writeUndefined writes the Undefined code (0x06) to the output stream
     */
    protected function writeUndefined() {
        $this->writeByte(6); // Undefined is only a  0x06 flag
    }

    /**
     * writeObjectEnd writes the object end code (0x009) to the output stream
     */
    protected function writeObjectEnd() {
        $this->writeInt(0); //  write the end object flag 0x00, 0x00, 0x09
        $this->writeByte(9);
    }

    /**
     * writeArrayOrObject first determines if the PHP array contains all numeric indexes
     * or a mix of keys.  Then it either writes the array code (0x0A) or the
     * object code (0x03) and then the associated data.
     *
     * @param array $d The php array
     */
    protected function writeArrayOrObject($d) {
        // referencing is disabled in arrays
        //Because if the array contains only primitive values,
        //Then === will say that the two arrays are strictly equal
        //if they contain the same values, even if they are really distinct
        $count = count($this->Amf0StoredObjects);
        if ($count <= self::MAX_STORED_OBJECTS) {
            $this->Amf0StoredObjects[$count] = & $d;
        }

        $numeric = array(); // holder to store the numeric keys
        $string = array(); // holder to store the string keys
        $len = count($d); // get the total number of entries for the array
        $largestKey = -1;
        foreach ($d as $key => $data) { // loop over each element
            if (is_int($key) && ($key >= 0)) { // make sure the keys are numeric
                $numeric[$key] = $data; // The key is an index in an array
                $largestKey = max($largestKey, $key);
            } else {
                $string[$key] = $data; // The key is a property of an object
            }
        }
        $num_count = count($numeric); // get the number of numeric keys
        $str_count = count($string); // get the number of string keys

        if (($num_count > 0 && $str_count > 0) ||
                ($num_count > 0 && $largestKey != $num_count - 1)) { // this is a mixed array
            $this->writeByte(8); // write the mixed array code
            $this->writeLong($num_count); // write  the count of items in the array
            $this->writeObjectFromArray($numeric + $string); // write the numeric and string keys in the mixed array
        } else if ($num_count > 0) { // this is just an array
            $num_count = count($numeric); // get the new count

            $this->writeByte(10); // write  the array code
            $this->writeLong($num_count); // write  the count of items in the array
            for ($i = 0; $i < $num_count; $i++) { // write all of the array elements
                $this->writeData($numeric[$i]);
            }
        } else if ($str_count > 0) { // this is an object
            $this->writeByte(3); // this is an  object so write the object code
            $this->writeObjectFromArray($string); // write the object name/value pairs
        } else { //Patch submitted by Jason Justman
            $this->writeByte(10); // make this  an array still
            $this->writeInt(0); //  give it 0 elements
            $this->writeInt(0); //  give it an element pad, this looks like a bug in Flash,
            //but keeps the next alignment proper
        }
    }

    /**
     * write reference
     * @param int $num
     */
    protected function writeReference($num) {
        $this->writeByte(0x07);
        $this->writeInt($num);
    }

    /**
     * writeObjectFromArray handles writing a php array with string or mixed keys.  It does
     * not write the object code as that is handled by the writeArrayOrObject and this method
     * is shared with the CustomClass writer which doesn't use the object code.
     *
     * @param array $d The php array with string keys
     */
    protected function writeObjectFromArray($d) {
        foreach ($d as $key => $data) { // loop over each element
            $this->writeUTF($key);  // write the name of the object
            $this->writeData($data); // write the value of the object
        }
        $this->writeObjectEnd();
    }

    /**
     *  handles writing an anoynous object (stdClass)
     *  can also be a reference
     *
     * @param stdClass $d The php object to write
     */
    protected function writeAnonymousObject($d) {
        if (!$this->handleReference($d, $this->Amf0StoredObjects)) {
            $this->writeByte(3);
            foreach ($d as $key => $data) { // loop over each element
                if ($key[0] != "\0") {
                    $this->writeUTF($key);  // write the name of the object
                    $this->writeData($data); // write the value of the object
                }
            }
            $this->writeObjectEnd();
        }
    }

    /**
     * writeTypedObject takes an instance of a class and writes the variables defined
     * in it to the output stream.
     * To accomplish this we just blanket grab all of the object vars with get_object_vars, minus the Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE field, whiuch is used as class name
     *
     * @param object $d The object to serialize the properties. The deserializer looks for Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE on this object and writes it as the class name.
     */
    protected function writeTypedObject($d) {
        if ($this->handleReference($d, $this->Amf0StoredObjects)) {
            return;
        }
        $this->writeByte(16); // write  the custom class code
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $className = $d->$explicitTypeField;
        if (!$className) {
            throw new Amfphp_Core_Exception(Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE . ' not found on a object that is to be sent as typed. ' . print_r($d, true));
        }
        unset($d->$explicitTypeField);
        $this->writeUTF($className); // write the class name
        $objVars = $d;
        foreach ($objVars as $key => $data) { // loop over each element
            if ($key[0] != "\0") {
                $this->writeUTF($key);  // write the name of the object
                $this->writeData($data); // write the value of the object
            }
        }
        $this->writeObjectEnd();
    }

    /**
     * writeData checks to see if the type was declared and then either
     * auto negotiates the type or relies on the user defined type to
     * serialize the data into Amf
     *
     * @param mixed $d The data
     */
    protected function writeData($d) {
        if ($this->packet->amfVersion == Amfphp_Core_Amf_Constants::AMF3_ENCODING) { //amf3 data. This is most often, so it's has been moved to the top to be first
            $this->writeByte(0x11);
            $this->writeAmf3Data($d);
            return;
        } elseif (is_int($d) || is_float($d)) { // double
            $this->writeNumber($d);
            return;
        } elseif (is_string($d)) { // string, long string
            $this->writeString($d);
            return;
        } elseif (is_bool($d)) { // boolean
            $this->writeBoolean($d);
            return;
        } elseif (is_null($d)) { // null
            $this->writeNull();
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Undefined) {
            $this->writeUndefined();
            return;
        } elseif (is_array($d)) { // array
            $this->writeArrayOrObject($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Date) { // date
            $this->writeDate($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Xml) { // Xml (note, no XmlDoc in AMF0)
            $this->writeXML($d);
            return;
        } elseif (is_object($d)) {
            if ($this->voConverter) {
                $this->voConverter->markExplicitType($d);
            }
            $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
            if (isset($d->$explicitTypeField)) {
                $this->writeTypedObject($d);
                return;
            } else {
                $this->writeAnonymousObject($d);
                return;
            }
        }
        throw new Amfphp_Core_Exception("couldn't write data " . print_r($d));
    }

    /*     * ******************************************************************************
     *                             Amf3 related code
     * ***************************************************************************** */

    /**
     * write amf 3 data
     * @todo no type markers ("\6', for example) in this method!
     * @param mixed $d
     */
    protected function writeAmf3Data($d) {
        if (is_int($d)) { //int
            $this->writeAmf3Number($d);
            return;
        } elseif (is_float($d)) { //double
            $this->outBuffer .= "\5";
            $this->writeDouble($d);
            return;
        } elseif (is_string($d)) { // string
            $this->outBuffer .= "\6";
            $this->writeAmf3String($d);
            return;
        } elseif (is_bool($d)) { // boolean
            $this->writeAmf3Bool($d);
            return;
        } elseif (is_null($d)) { // null
            $this->writeAmf3Null();
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Undefined) { // undefined
            $this->writeAmf3Undefined();
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Date) { // date
            $this->writeAmf3Date($d);
            return;
        } elseif (is_array($d)) { // array
            $this->writeAmf3Array($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_ByteArray) { //byte array
            $this->writeAmf3ByteArray($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Xml) { // Xml
            $this->writeAmf3Xml($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_XmlDocument) { // XmlDoc
            $this->writeAmf3XmlDocument($d);
            return;
        } elseif ($d instanceof Amfphp_Core_Amf_Types_Vector) {
            $this->writeAmf3Vector($d);
            return;
        } elseif (is_object($d)) {
            if ($this->voConverter) {
                $this->voConverter->markExplicitType($d);
            }
            $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
            if (isset($d->$explicitTypeField)) {
                $this->writeAmf3TypedObject($d);
                return;
            } else {
                $this->writeAmf3AnonymousObject($d);
                return;
            }
        }
        throw new Amfphp_Core_Exception("couldn't write object " . print_r($d, false));
    }

    /**
     * Write undefined (Amf3).
     *
     * @return nothing
     */
    protected function writeAmf3Undefined() {
        $this->outBuffer .= "\0";
    }

    /**
     * Write NULL (Amf3).
     *
     * @return nothing
     */
    protected function writeAmf3Null() {
        $this->outBuffer .= "\1";
    }

    /**
     * Write a boolean (Amf3).
     *
     * @param bool $d the boolean to serialise
     *
     * @return nothing
     */
    protected function writeAmf3Bool($d) {
        $this->outBuffer .= $d ? "\3" : "\2";
    }

    /**
     * Write an (un-)signed integer (Amf3).
     *
     * @see getAmf3Int()
     *
     * @param int $d the integer to serialise
     *
     * @return nothing
     */
    protected function writeAmf3Int($d) {
        $this->outBuffer .= $this->getAmf3Int($d);
    }

    /**
     * Write a string (Amf3). Strings are stored in a cache and in case the same string
     * is written again, a reference to the string is sent instead of the string itself.
     *
     * note: Sending strings larger than 268435455 (2^28-1 byte) will (silently) fail!
     *
     * note: The string marker is NOT sent here and has to be sent before, if needed.
     *
     *
     * @param string $d the string to send
     *
     * @return The reference index inside the lookup table is returned. In case of an empty
     * string which is sent in a special way, NULL is returned.
     */
    protected function writeAmf3String($d) {

        if ($d === '') {
            //Write 0x01 to specify the empty string ('UTF-8-empty')
            $this->outBuffer .= "\1";
            return;
        }

        if (!$this->handleReference($d, $this->storedStrings)) {
            $this->writeAmf3Int(strlen($d) << 1 | 1); // U29S-value
            $this->outBuffer .= $d;
        }
    }

    /**
     *  handles writing an anoynous object (stdClass)
     *  can also be a reference
     * Also creates a bogus traits entry, as even an anonymous object has traits. In this way a reference to a class trait will have the right id.
     * @todo it would seem that to create only one traits entry for an anonymous object would be the way to go. this 
     * however messes things up in both Flash and Charles Proxy. For testing call discovery service using AMF. investigate.
     *
     * @param stdClass $d The php object to write
     * @param doReference Boolean This is used by writeAmf3Array, where the reference has already been taken care of, 
     * so there this method is called with false
     */
    protected function writeAmf3AnonymousObject($d, $doReference = true) {

        //Write the object tag
        $this->outBuffer .= "\12";
        if ($doReference && $this->handleReference($d, $this->storedObjects)) {
            return;
        }

        //bogus class traits entry
        $this->className2TraitsInfo[] = array();

        //anonymous object. So type this as a dynamic object with no sealed members.
        //U29O-traits : 1011.
        $this->writeAmf3Int(0xB);
        //no class name. empty string for anonymous object
        $this->writeAmf3String("");
        //name/value pairs for dynamic properties
        foreach ($d as $key => $value) {
            $this->writeAmf3String($key);
            $this->writeAmf3Data($value);
        }
        //empty string, marks end of dynamic members
        $this->outBuffer .= "\1";
    }

    /**
     * write amf3 array
     * @param array $d
     */
    protected function writeAmf3Array(array $d) {
        // referencing is disabled in arrays
        //Because if the array contains only primitive values,
        //Then === will say that the two arrays are strictly equal
        //if they contain the same values, even if they are really distinct
        $count = count($this->storedObjects);
        if ($count <= self::MAX_STORED_OBJECTS) {
            $this->storedObjects[$count] = & $d;
        }

        $numeric = array(); // holder to store the numeric keys >= 0
        $string = array(); // holder to store the string keys; actually, non-integer or integer < 0 are stored
        $len = count($d); // get the total number of entries for the array
        $largestKey = -1;
        foreach ($d as $key => $data) { // loop over each element
            if (is_int($key) && ($key >= 0)) { // make sure the keys are numeric
                $numeric[$key] = $data; // The key is an index in an array
                $largestKey = max($largestKey, $key);
            } else {
                $string[$key] = $data; // The key is a property of an object
            }
        }

        $num_count = count($numeric); // get the number of numeric keys
        $str_count = count($string); // get the number of string keys

        if (
                ($str_count > 0 && $num_count == 0) || // Only strings or negative integer keys are present.
                ($num_count > 0 && $largestKey != $num_count - 1) // Non-negative integer keys are present, but the array is not 'dense' (it has gaps).
        ) {
            //// this is a mixed array. write it as an anonymous/dynamic object  with no sealed members
            $this->writeAmf3AnonymousObject($numeric + $string, false);
        } else { // this is just an array
            $this->outBuffer .= "\11";
            $num_count = count($numeric);
            $handle = $num_count * 2 + 1;
            $this->writeAmf3Int($handle);

            foreach ($string as $key => $val) {
                $this->writeAmf3String($key);
                $this->writeAmf3Data($val);
            }
            $this->writeAmf3String(''); //End start hash

            for ($i = 0; $i < $num_count; $i++) {
                $this->writeAmf3Data($numeric[$i]);
            }
        }
    }

    /**
     * Return the serialisation of the given integer (Amf3).
     *
     * note: There does not seem to be a way to distinguish between signed and unsigned integers.
     * This method just sends the lowest 29 bit as-is, and the receiver is responsible to interpret
     * the result as signed or unsigned based on some context.
     *
     * note: The limit imposed by Amf3 is 29 bit. So in case the given integer is longer than 29 bit,
     * only the lowest 29 bits will be serialised. No error will be logged!
     * @TODO refactor into writeAmf3Int
     *
     * @param int $d the integer to serialise
     *
     * @return string
     */
    protected function getAmf3Int($d) {

        /**
         * @todo The lowest 29 bits are kept and all upper bits are removed. In case of
         * an integer larger than 29 bits (32 bit, 64 bit, etc.) the value will effectively change! Maybe throw an exception!
         */
        $d &= 0x1fffffff;

        if ($d < 0x80) {
            return
                    chr($d);
        } elseif ($d < 0x4000) {
            return
                    chr($d >> 7 & 0x7f | 0x80) .
                    chr($d & 0x7f);
        } elseif ($d < 0x200000) {
            return
                    chr($d >> 14 & 0x7f | 0x80) .
                    chr($d >> 7 & 0x7f | 0x80) .
                    chr($d & 0x7f);
        } else {
            return
                    chr($d >> 22 & 0x7f | 0x80) .
                    chr($d >> 15 & 0x7f | 0x80) .
                    chr($d >> 8 & 0x7f | 0x80) .
                    chr($d & 0xff);
        }
    }

    /**
     * write Amf3 Number
     * @param number $d
     */
    protected function writeAmf3Number($d) {
        if (is_int($d) && $d >= -268435456 && $d <= 268435455) {//check valid range for 29bits
            $this->outBuffer .= "\4";
            $this->writeAmf3Int($d);
        } else {
            //overflow condition would occur upon int conversion
            $this->outBuffer .= "\5";
            $this->writeDouble($d);
        }
    }

    /**
     * write Amfphp_Core_Amf_Types_Xml in amf3
     * @param Amfphp_Core_Amf_Types_Xml $d
     */
    protected function writeAmf3Xml(Amfphp_Core_Amf_Types_Xml $d) {
        $d = preg_replace('/\>(\n|\r|\r\n| |\t)*\</', '><', trim($d->data));
        $this->writeByte(0x0B);
        $this->writeAmf3String($d);
    }

    /**
     * write Amfphp_Core_Amf_Types_XmlDocument in amf3
     * @param Amfphp_Core_Amf_Types_XmlDocument $d
     */
    protected function writeAmf3XmlDocument(Amfphp_Core_Amf_Types_XmlDocument $d) {
        $d = preg_replace('/\>(\n|\r|\r\n| |\t)*\</', '><', trim($d->data));
        $this->writeByte(0x07);
        $this->writeAmf3String($d);
    }

    /**
     * write Amfphp_Core_Amf_Types_Date in amf 3
     * @param Amfphp_Core_Amf_Types_Date $d
     */
    protected function writeAmf3Date(Amfphp_Core_Amf_Types_Date $d) {
        $this->writeByte(0x08);
        $this->writeAmf3Int(1);
        $this->writeDouble($d->timeStamp);
    }

    /**
     * write Amfphp_Core_Amf_Types_ByteArray in amf3
     * @param Amfphp_Core_Amf_Types_ByteArray $d
     */
    protected function writeAmf3ByteArray(Amfphp_Core_Amf_Types_ByteArray $d) {
        $this->writeByte(0x0C);
        $data = $d->data;
        if (!$this->handleReference($data, $this->storedObjects)) {
            $obj_length = strlen($data);
            $this->writeAmf3Int($obj_length << 1 | 0x01);
            $this->outBuffer .= $data;
        }
    }

    /**
     * looks if $obj already has a reference. If it does, write it, and return true. If not, add it to the references array.
     * Depending on whether or not the spl_object_hash function can be used ( available (PHP >= 5.2), and can only be used on an object)
     * things are handled a bit differently:
     * - if possible, objects are hashed and the hash is used as a key to the references array. So the array has the structure hash => reference
     * - if not, the object is pushed to the references array, and array_search is used. So the array has the structure reference => object.
     * maxing out the number of stored references improves performance(tested with an array of 9000 identical objects). This may be because isset's performance
     * is linked to the size of the array. weird...
     * note on using $references[$count] = &$obj; rather than 
     * $references[] = &$obj;
     * the first one is right, the second is not, as with the second one we could end up with the following:
     * some object hash => 0, 0 => array. (it should be 1 => array)
     * 
     * This also means that 2 completely separate instances of a class but with the same values will be written fully twice if we can't use the hash system
     * 
     * @param mixed $obj
     * @param array $references
     */
    protected function handleReference(&$obj, array &$references) {
        $key = false;
        $count = count($references);
        if (is_object($obj) && function_exists('spl_object_hash')) {
            $hash = spl_object_hash($obj);
            if (isset($references[$hash])) {
                $key = $references[$hash];
            } else {
                if ($count <= self::MAX_STORED_OBJECTS) {
                    //there is some space left, store object for reference
                    $references[$hash] = $count;
                }
            }
        } else {
            //no hash available, use array with simple numeric keys
            $key = array_search($obj, $references, TRUE);

            if (($key === false) && ($count <= self::MAX_STORED_OBJECTS)) {
                // $key === false means the object isn't already stored
                // count... means there is still space
                //so only store if these 2 conditions are met
                $references[$count] = &$obj;
            }
        }

        if ($key !== false) {
            //reference exists. write it and return true
            if ($this->packet->amfVersion == Amfphp_Core_Amf_Constants::AMF0_ENCODING) {
                $this->writeReference($key);
            } else {
                $handle = $key << 1;
                $this->writeAmf3Int($handle);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * writes a typed object. Type is determined by having an "explicit type" field. If this field is 
     * not set, call writeAmf3AnonymousObject
     * write all properties as sealed members.
     * @param object $d
     */
    protected function writeAmf3TypedObject($d) {
        //Write the object tag
        $this->outBuffer .= "\12";
        if ($this->handleReference($d, $this->storedObjects)) {
            return;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;

        $className = $d->$explicitTypeField;
        $propertyNames = null;

        if (isset($this->className2TraitsInfo[$className])) {
            //we have traits information and a reference for it, so use a traits reference
            $traitsInfo = $this->className2TraitsInfo[$className];
            $propertyNames = $traitsInfo['propertyNames'];
            $referenceId = $traitsInfo['referenceId'];
            $traitsReference = $referenceId << 2 | 1;
            $this->writeAmf3Int($traitsReference);
        } else {
            //no available traits information. Write the traits
            $propertyNames = array();
            foreach ($d as $key => $value) {
                if ($key[0] != "\0" && $key != $explicitTypeField) { //Don't write protected properties or explicit type
                    $propertyNames[] = $key;
                }
            }

            //U29O-traits:  0011 in LSBs, and number of properties
            $numProperties = count($propertyNames);
            $traits = $numProperties << 4 | 3;
            $this->writeAmf3Int($traits);
            //class name
            $this->writeAmf3String($className);
            //list of property names
            foreach ($propertyNames as $propertyName) {
                $this->writeAmf3String($propertyName);
            }

            //save for reference
            $traitsInfo = array('referenceId' => count($this->className2TraitsInfo), 'propertyNames' => $propertyNames);
            $this->className2TraitsInfo[$className] = $traitsInfo;
        }
        //list of values
        foreach ($propertyNames as $propertyName) {
            $this->writeAmf3Data($d->$propertyName);
        }
    }
    
    /**
     * write vector
     * @param Amfphp_Core_Amf_Types_Vector $d 
     */
    protected function writeAMF3Vector(Amfphp_Core_Amf_Types_Vector $d) {
        //Write the vector tag
        $this->writeByte($d->type);
        // referencing is disabled in vectors as in arrays
        //Because if the array contains only primitive values,
        //Then === will say that the two arrays are strictly equal
        //if they contain the same values, even if they are really distinct
        $count = count($this->storedObjects);
        if ($count <= self::MAX_STORED_OBJECTS) {
            $this->storedObjects[$count] = & $d;
        }
        $num_count = count($d->data);

        $handle = $num_count * 2 + 1;
        $this->writeAmf3Int($handle);

        $this->writeByte($d->fixed);

        if ($d->type === Amfphp_Core_Amf_Types_Vector::VECTOR_OBJECT) {
            $className = $d->className;

            if ($className == "String" or $className == "Boolean") {
                $this->writeByte(0x01);
                $function = "writeAmf3Data";
            } else {
                $this->writeAmf3String($className);
                $function = "writeAmf3TypedObject";
            }
        } else {
            if ($d->type == Amfphp_Core_Amf_Types_Vector::VECTOR_INT) {
                $className = "i";
            } elseif ($d->type == Amfphp_Core_Amf_Types_Vector::VECTOR_UINT) {
                $className = "I";
            } elseif ($d->type == Amfphp_Core_Amf_Types_Vector::VECTOR_DOUBLE) {
                $className = "d";
            }
            $function = "writeAmf3VectorValue";
        }


        for ($i = 0; $i < $num_count; $i++) {
            $this->$function($d->data[$i], $className);
        }

    }

    /**
     * Writes numeric values for int, uint and double (floating point) vectors to the AMF byte stream. 
     * 
     * @param   mixed   But should be either an integer (signed or unsigned) or a floating point.
     * @param   string  'i' for signed integers, 'I' for unsigned integers, and 'd' for double precision floating point
     */
    function writeAmf3VectorValue($value, $format) {
        $bytes = pack($format, $value);
        if (Amfphp_Core_Amf_Util::isSystemBigEndian()) {
            $bytes = strrev($bytes);
        }
        $this->outBuffer .= $bytes;
    }



}

?>