<?php
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * For more information, please see {@link http://gwtphp.sourceforge.com/}.</p>
 * 
 * 
 * <p>Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at</p>
 * 
 * {@link http://www.apache.org/licenses/LICENSE-2.0 http://www.apache.org/licenses/LICENSE-2.0}
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * @package gwtphp.rpc.impl
 */


require_once(GWTPHP_DIR.'/rpc/SerializationPolicy.class.php');
//require_once(GWTPHP_DIR.'/lang/ReflectionHelper.class.php');


/**
 * A serialization policy compatible with GWT 1.3.3 RPC. This is used when no
 * serialization policy file is present.
 * 
 * <p>
 * The set of allowed types are:
 * </p>
 * <ol>
 * <li>Primitives</li>
 * <li>Types assignable to {@link IsSerializable}</li>
 * <li>Types with custom field serializers</li>
 * <li>Arrays of the above types</li>
 * </ol>
 * <p>
 * Types that derive from {@link Serializable} but do not meet any of the above
 * criteria may not be serialized as leaf types. However, their fields may be
 * serialized as super types of a legal type.
 * </p>
 */
class LegacySerializationPolicy extends SerializationPolicy  {

	
	/**
   * Many JRE types would appear to be {@link Serializable} on the server.
   * However, clients would not see these types as being {@link Serializable}
   * due to mismatches between the GWT JRE emulation and the real JRE. As a
   * workaround, this blacklist specifies a list of problematic types which
   * should be seen as not implementing {@link Serializable} for the purpose
   * matching the client's expectations. Note that a type on this list may still
   * be serializable via a custom serializer.
   */
	//TODO: upewnic sie ze ta lista ma tak wygladac
  private static  $JRE_BLACKLIST = array (
      'java.lang.ArrayStoreException.class', 'java.lang.AssertionError.class',
      'java.lang.Boolean.class');
       
//      java.lang.Byte.class, java.lang.Character.class,
//      java.lang.Class.class, java.lang.ClassCastException.class,
//      java.lang.Double.class, java.lang.Error.class, java.lang.Exception.class,
//      java.lang.Float.class, java.lang.IllegalArgumentException.class,
//      java.lang.IllegalStateException.class,
//      java.lang.IndexOutOfBoundsException.class, java.lang.Integer.class,
//      java.lang.Long.class, java.lang.NegativeArraySizeException.class,
//      java.lang.NullPointerException.class, java.lang.Number.class,
//      java.lang.NumberFormatException.class, java.lang.RuntimeException.class,
//      java.lang.Short.class, java.lang.StackTraceElement.class,
//      java.lang.String.class, java.lang.StringBuffer.class,
//      java.lang.StringIndexOutOfBoundsException.class,
//      java.lang.Throwable.class, java.lang.UnsupportedOperationException.class,
//      java.util.ArrayList.class,
//      java.util.ConcurrentModificationException.class, java.util.Date.class,
//      java.util.EmptyStackException.class, java.util.EventObject.class,
//      java.util.HashMap.class, java.util.HashSet.class,
//      java.util.MissingResourceException.class,
//      java.util.NoSuchElementException.class, java.util.Stack.class,
//      java.util.TooManyListenersException.class, java.util.Vector.class};

/**
 * Enter description here...
 *
 * @var LegacySerializationPolicy
 */
private static $sInstance;// = 

/**
 * Enter description here...
 *
 * @return LegacySerializationPolicy
 */
  public static function getInstance() {
   return (LegacySerializationPolicy::$sInstance === null) ?  LegacySerializationPolicy::$sInstance = new LegacySerializationPolicy() : LegacySerializationPolicy::$sInstance;
  //  return LegacySerializationPolicy::$sInstance;
  }
  
  private function __construct() {
  	
  }

 /**
   * Returns <code>true</code> if the class' fields should be deserialized.
   * 
   * @param MappedClass $mappedClass the class to test
   * @return boolean <code>true</code> if the class' fields should be deserialized
   */
  public  function shouldDeserializeFields(MappedClass $mappedClass) {
  	return $this->isFieldSerializable($mappedClass);
  }

  /**
   * Returns <code>true</code> if the class' fields should be serialized.
   * 
   * @param MappedClass $mappedClass the class to test
   * @return boolean <code>true</code> if the class' fields should be serialized
   */
  public function shouldSerializeFields(MappedClass $mappedClass) {
  	 return $this->isFieldSerializable($mappedClass);
  }

  /**
   * Validates that the specified class should be deserialized from a stream.
   * 
   * @param MappedClass $mappedClass  the class to validate
   * @throws SerializationException if the class is not allowed to be
   *           deserialized
   * @return void
   */
  public function validateDeserialize(MappedClass $mappedClass) {
  if (!$this->isInstantiable($mappedClass)) {
  	  require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
      throw new SerializationException(
          'Type \''
              + $mappedClass->getSignature()
              + '\' did not have a custom field serializer.  For security purposes, this type will not be serialized.');
    }
  	
  }

  /**
   * Validates that the specified class should be serialized into a stream.
   * 
   * @param MappedClass $mappedClass the class to validate
   * @throws SerializationException if the class is not allowed to be serialized
   * @return void
   */
  public function validateSerialize(MappedClass $mappedClass) {
  	 if (!$this->isInstantiable($mappedClass)) {
  	 	
  	  require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
      throw new SerializationException(
          'Type \''
              + $mappedClass->getSignature()
              + '\' did not have a custom field serializer.  For security purposes, this type will not be serialized.');
    }
  	
  }
  
  /**
   * Field serializable types are primitives, {@line IsSerializable},
   * {@link Serializable}, types with custom serializers, and any arrays of
   * those types.
   * @param MappedClass $mappedClass signature
   * @return boolean
   */
  private function isFieldSerializable($mappedClass) {
    if ($this->isInstantiable($mappedClass)) {
      return true;
    }
   // if (Serializable.class.isAssignableFrom(clazz)) {
   //   return !JRE_BLACKSET.contains(clazz);
   // }
    return false;
  }

  
   /**
   * Instantiable types are primitives, {@line IsSerializable}, types with
   * custom serializers, and any arrays of those types. Merely
   * {@link Serializable} types cannot be instantiated or serialized directly
   * (only as super types of legacy serializable types).
   * @param MappedClass $mappedClass 
   * @return boolean
   */
  private function isInstantiable(MappedClass $mappedClass) {
  	if ($mappedClass->isPrimitive()) {
  		return true;
  	}
    if ($mappedClass->isArray()) {
    	return $this->isInstantiable($mappedClass->getComponentType());
    }
    if ($mappedClass->getReflectionClass() != null && $mappedClass->getReflectionClass()->isSubclassOf(SerializationPolicy::$IS_SERIALIZABLE_INTERFACE_CLASS_NAME)) {
    	return true;
    }
    //if (IsSerializable.class.isAssignableFrom(clazz)) {
    //  return true;
    //}
    return SerializabilityUtil::hasCustomFieldSerializer($mappedClass) != null;
  }
  
  	
	
}


?>
