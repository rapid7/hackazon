<?php
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * Additional modifications, GWT generators and linkers by Yifei Teng. All rights reserved.<br>
 * For more information, please see {@link https://github.com/tengyifei/gwtphp}</p>
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

require_once(GWTPHP_DIR.'/rpc/impl/AbstractSerializationStream.class.php');
require_once(GWTPHP_DIR.'/rpc/SerializationStreamReader.class.php');


abstract class AbstractSerializationStreamReader extends AbstractSerializationStream
implements SerializationStreamReader {

	/**
	 *
	 * @var array
	 */
	private $seenArray = array();

	/**
	 *
	 * @throws SerializationException
	 * @param string $encoded
	 * @return void
	 */
	public function prepareToRead($encodedTocens = null) {
		$this->seenArray = array();
		$this->setVersion($this->readInt()); // Read the stream version number
		$this->setFlags($this->readInt()); // Read the flags from the stream
	}

	/**
	 *
	 * @throws SerializationException
	 * @param string $encoded
	 * @return Object
	 */
	public final function readObject() {
		$token = $this->readInt();

		if ($token < 0) {
			// Negative means a previous object
			// Transform negative 1-based to 0-based.
			return $this->seenArray[(-($token + 1))];
		}

		// Positive means a new object
		$typeSignature =  $this->getString($token);
		if ($typeSignature === null) {
			// a null string means a null instance
			return null;
		}

		return  $this->deserialize($typeSignature);
	}

	/**
	 *Deserialize an object with the given type signature.
	 * 
	 * @throws SerializationException
	 * @param string $typeSignature  the type signature to deserialize
	 * @return Object the deserialized object
	 */
	protected abstract function deserialize($typeSignature);

	/**
   * Gets a string out of the string table.
   * 
   * @param int $index the index of the string to get
   * @return string
   */
	protected abstract function getString($index);

	/**
	 *
	 * @throws SerializationException
	 * @param Object $obj
	 * @return void
	 */
	protected final function rememberDecodedObject($index,&$obj) {
		$this->seenArray[$index-1] = $obj;
	}
	
	/**
	 * 
	 *
	 * @return int
	 */
	  protected final function reserveDecodedObjectIndex() {
	    $this->seenArray[] = null;
	    // index is 1-based
	    return count($this->seenArray);
	  }
	

}

?>