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
 * @package gwtphp.rpc.core.java.util
 */

require_once(GWTPHP_DIR.'/maps/java/util/HashMap.class.php');

final class HashMap_CustomFieldSerializer {

	private static $ACCEPTABLE_KEY_TYPES = array (
	'java.lang.String' => true,
	'java.lang.Byte' => true,
	'java.lang.Character' => true,
	'java.lang.Double' => true,
	'java.lang.Float' => true,
	'java.lang.Integer' => true,
	'java.lang.Long' => true,
	'java.lang.Short' => true,
	);

	/**
	 *
	 * @param SerializationStreamReader $streamReader
	 * @return array
	 * @throws SerializationException
	 */
	public static function instantiate(SerializationStreamReader $streamReader) {
		return (FORCE_CAST_TO_PHP_PRIMITIVE_TYPES) ? array() : new HashMap();
		//return array();
		//return new HashMap();
	}
	/**
	 * 
	 *
	 * @param SerializationStreamReader $streamReader
	 * @param HashMap $instance
	 * @throws SerializationException 
	 */
	public static function deserialize(SerializationStreamReader $streamReader,
	/*HashMap<Object, Object>*/$instance){
		$consuming_type = 0; // HashMap
		if ($instance instanceof HashMap) {
			$consuming_type = 0;
		} else if (is_array($instance)) {
			$consuming_type = 1;
		} else {
			require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
			throw new SerializationException("Error occurred while deserialize HashMap: "
			."HashMap_CustomFieldSerializer deserialize only array() or HashMap object, but given: "
			.gettype($instance));
		}

		$size = $streamReader->readInt();

		for ($i = 0; $i < $size; ++$i) {
			/*Object*/ $key = $streamReader->readObject();
			/*Object*/ $value = $streamReader->readObject();
			if (0 == $consuming_type) {
				$instance->put($key, $value);
			} else {
				if (!FORCE_CAST_TO_PHP_PRIMITIVE_TYPES || is_object($key)) {
					echo $key.' : '.gettype($key);
					require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
					throw new SerializationException("Error occurred while casting native php array to HashMap: "
					."HashMap_CustomFieldSerializer serialize only array() where"
					." keys object are mapped by one of following types:"
					." java.lang.String, java.lang.Byte, java.lang.Character, java.lang.Double, "
					."java.lang.Float, java.lang.Integer, java.lang.Long, java.lang.Short , but given: "
					.gettype($key));


				}
				$instance[$key] = $value;
			}
		}

	}


	/**
  *
  * @param SerializationStreamWriter $streamWriter
  * @param HashMap $instance
  * @param MappedClass $instanceClass
  * @throws SerializationException
  */
	public static function serialize(SerializationStreamWriter $streamWriter,
	/*HashMap<Object, Object>*/ $instance, MappedClass $instanceClass)  {
		if ($instance instanceof HashMap) {
			$size = $instance->size();
			$streamWriter->writeInt($size);
			//assert($instanceClass->isGeneric()); //jesli nie array to zapomniano w gwtphpmap dodacgenericsow
			if ($instanceClass->isGeneric()) {
				$typeParameters = $instanceClass->getTypeParameters();
			}
			else $typeParameters = array();
			assert(is_array($typeParameters)); //jesli nie array to zapomniano w gwtphpmap dodacgenericsow
			foreach ($instance->getKeySet() as $key) {
				
				$streamWriter->writeObject($key,$typeParameters[0]);
				$streamWriter->writeObject($instance->get($key),$typeParameters[1]);
			}
		} else if (is_array($instance)) { // $instance is array
			//$size = $instance->size();
			$size = count($instance);
			$streamWriter->writeInt($size);

			//for (Object obj : instance) {
			if (!$instanceClass->isGeneric())  {
				require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
				throw new SerializationException("Error occurred while casting native php array to HashMap: "
				."HashMap must be mapped as generic type! add < > to signatures and CRC");
			}
			$typeParameters = $instanceClass->getTypeParameters();

			if ( !isset(HashMap_CustomFieldSerializer::$ACCEPTABLE_KEY_TYPES[$typeParameters[0]->getSignature()]) ) {
				require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
				throw new SerializationException("Error occurred while casting native php array to HashMap: "
				."HashMap_CustomFieldSerializer serialize only array() where "
				."keys object are mapped by one of following types: "
				."java.lang.String, java.lang.Byte, java.lang.Character, java.lang.Double, "
				."java.lang.Float, java.lang.Integer, java.lang.Long, java.lang.Short, but given: "
				.$typeParameters[0]->getSignature());
			}

			foreach ($instance as $key => $obj) {
				$streamWriter->writeObject($key,$typeParameters[0]);
				$streamWriter->writeObject($obj,$typeParameters[1]);
			}
		}
		//for (Entry<Object, Object> entry : instance.entrySet()) {
		//  streamWriter.writeObject(entry.getKey());
		//  streamWriter.writeObject(entry.getValue());
		//}
		else {
			require_once(GWTPHP_DIR.'/maps/java/lang/UnimplementedOperationException.class.php');
			throw new UnimplementedOperationException("HashMap_CustomFieldSerializer serialize type: "+gettype($instance) + " not implemented");
		}
	}

}

?>