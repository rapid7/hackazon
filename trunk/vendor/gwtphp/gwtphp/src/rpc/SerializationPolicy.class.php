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
 * @package gwtphp.rpc
 */


/**
 * This is an abstract class for representing the serialization policy for a
 * given module and
 * {@link com.google.gwt.user.client.rpc.RemoteService RemoteService}.
 */
abstract class SerializationPolicy {
	/**
	 * @var String
	 */
  public static $IS_SERIALIZABLE_INTERFACE_CLASS_NAME = 'IsSerializable';
	
	
  /**
   * Returns <code>true</code> if the class' fields should be deserialized.
   * 
   * @param MappedClass $mappedClass the class to test
   * @return boolean <code>true</code> if the class' fields should be deserialized
   */
  public abstract function shouldDeserializeFields(MappedClass $mappedClass);

  /**
   * Returns <code>true</code> if the class' fields should be serialized.
   * 
   * @param MappedClass  $mappedClass the class to test
   * @return boolean <code>true</code> if the class' fields should be serialized
   */
  public abstract function shouldSerializeFields(MappedClass $mappedClass);

  /**
   * Validates that the specified class should be deserialized from a stream.
   * 
   * @param MappedClass $mappedClass the class to validate
   * @throws SerializationException if the class is not allowed to be
   *           deserialized
   * @return void
   */
  public abstract function validateDeserialize(MappedClass $mappedClass);

  /**
   * Validates that the specified class should be serialized into a stream.
   * 
   * @param MappedClass $mappedClass the class to validate
   * @throws SerializationException if the class is not allowed to be serialized
   * @return void
   */
  public abstract function validateSerialize(MappedClass $mappedClass);
}

?>