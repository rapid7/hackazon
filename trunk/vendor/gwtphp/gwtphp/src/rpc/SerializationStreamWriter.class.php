<?PHP
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
 * An interface for writing values from a stream.
 */
interface SerializationStreamWriter {

	/**
	 * 
	 * @param boolean $value
	 */
	public function writeBoolean($value) ;

	/**
	 * 
	 * @param byte $value
	 */
	public function writeByte($value) ;
	/**
	 * 
	 * @param char $value
	 */
	public function writeChar($value) ;
	/**
	 * 
	 * @param double $value
	 */
	public function writeDouble($value) ;
	/**
	 * 
	 * @param float $value
	 */
	public function writeFloat($value) ;
	/**
	 * 
	 * @param int $value
	 */
	public function writeInt($value) ;
	/**
	 * 
	 * @param long $value
	 */
	public function writeLong($value) ;
	/**
	 * 
	 * @param Object $value
	 * @param MappedClass $type
	 */
	public function writeObject($value,MappedClass $type = null) ;
	/**
	 * 
	 * @param short $value
	 */
	public function writeShort($value) ;
	/**
	 * 
	 * @param string $value
	 */
	public function writeString($value) ;

}


?>