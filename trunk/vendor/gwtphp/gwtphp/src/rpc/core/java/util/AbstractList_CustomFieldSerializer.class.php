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

abstract  class AbstractList_CustomFieldSerializer {

	
	/**
	 *
	 * @param SerializationStreamReader $streamReader
	 * @param ArrayList<Object> $instance
	 * @throws SerializationException 
	 */
	public static function deserialize(SerializationStreamReader $streamReader,
	$instance,$consuming_type,$name) {
		if ($consuming_type < 0 ) {
			require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
			throw new SerializationException("Error occurred while deserialize $name: "
			.$name."_CustomFieldSerializer deserialize only array() or "
			."$name object, but given: "
			.gettype($instance));
		}

		$size = $streamReader->readInt();
		
		for ($i = 0; $i < $size; ++$i) {
			/*Object*/ $obj = $streamReader->readObject();
			if (0 == $consuming_type) {
				$instance->add($obj);
			} else {
				$instance[$i] = $obj;
			}
			
		}
	}
	/**
 * Enter description here...
 *
 * @param SerializationStreamWriter $streamWriter
 * @param array $instance
 * @param MappedClass $instanceClass
 * @throws SerializationException
 */
	public static function serialize(SerializationStreamWriter $streamWriter,
	/*ArrayList<Object>*/ $instance, MappedClass $instanceClass,$consuming_type,$name)  {		
		if ($consuming_type == 0) {
			$size = $instance->size();
			$streamWriter->writeInt($size);
			
			$iterator = $instance->getIterator();

			while($iterator->valid()) {			   
				$streamWriter->writeObject( $iterator->current());
			    $iterator->next();
			}
			
		}  else if ($consuming_type == 1) { // $instance is array
		
			$size = count($instance);
			$streamWriter->writeInt($size);
			//for (Object obj : instance) {
			if (!$instanceClass->isGeneric())  {
				require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
				throw new SerializationException("Error occurred while casting native php array to $name: "
			."$name must be mapped as generic type! add < > to signatures and CRC");
			}
			
//			if (!$instanceClass->isGeneric())  {
//				class_exists('SerializationException') || require(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
//				throw new SerializationException("ArrayList must be mapped as generic type! add < > to signatures and CRC");
//			}
			$typeParameters = $instanceClass->getTypeParameters();
			foreach ($instance as $obj) {
				$streamWriter->writeObject($obj,$typeParameters[0]);
			}
		}
	}
}

?>