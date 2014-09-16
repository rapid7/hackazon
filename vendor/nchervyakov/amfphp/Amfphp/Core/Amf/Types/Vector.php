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
 * A wrapper class that tells the serializer that we want a vector at the flash end. You should only populate the data array
 * with instances of the same class. If you want to send back objects of different types, use the array class and not
 * Vector.<*>, as Vector.<*> is not and will not be supported.
 * @package Amfphp_Core_Amf_Types
 * @author Mick Powell
 */
class Amfphp_Core_Amf_Types_Vector {
    /**
     * vector object type.
     */
    const VECTOR_OBJECT = 0x10;
    /**
     * vector int type.
     */
    const VECTOR_INT = 0x0D;
    /**
     * vector uint type.
     */
    const VECTOR_UINT = 0x0E;
    /**
     * vector uint type.
     */
    const VECTOR_DOUBLE = 0x0F;
    /**
     * vector fixed length type.
     */
    const VECTOR_FIXED_LENGTH = 0x01;
    /**
     * vector fixed length type.
     */
    const VECTOR_VARIABLE_LENGTH = 0x00;

    /**
     * An array that holds instances of the same class or data type only.
     */
    public $data;

    /**
     * Is the vector fixed length? VECTOR_VARIABLE_LENGTH for not-fixed length, VECTOR_FIXED_LENGTH for fixed. The default is VECTOR_VARIABLE_LENGTH.
     */
    public $fixed = self::VECTOR_VARIABLE_LENGTH;

    /**
     * The type of vector.  VECTOR_INT for int, VECTOR_UINT for uint, VECTOR_DOUBLE for float, and VECTOR_OBJECT for object. The default is object.
     */
    public $type = self::VECTOR_OBJECT;

    /**
     * The class of the vector if the vector is an object vector. You should set this and not $_explicitType on individual objects now. The latter
     * will be phased out.
     */
    public $className;

}

?>