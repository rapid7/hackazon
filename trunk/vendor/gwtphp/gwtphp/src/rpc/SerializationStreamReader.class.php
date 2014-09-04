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


/*
 The cast types allowed are:
(int), (integer) - cast to integer
(bool), (boolean) - cast to boolean
(float), (double), (real) - cast to float
(string) - cast to string
(array) - cast to array
(object) - cast to object

 */
interface  SerializationStreamReader {

	/**
	 * @throws SerializationException
	 * @return boolean
	 */
	function readBoolean();


	/**
	 * @throws SerializationException
	 * @return byte
	 */
	function readByte() ;

	/**
	 * @throws SerializationException
	 * @return char
	 */
	function readChar() ;
	/**
	 * @throws SerializationException
	 * @return double
	 */
	function readDouble() ;

	/**
	 * @throws SerializationException
	 * @return float
	 */
	function readFloat() ;

	/**
	 * @throws SerializationException
	 * @return int
	 */
	function readInt() ;

	/**
	 * @throws SerializationException
	 * @return float
	 */
	function readLong() ;

	/**
	 * @throws SerializationException
	 * @return Object
	 */
	function readObject() ;

	/**
	 * @throws SerializationException
	 * @return short
	 */
	function readShort() ;

	/**
	 * @throws SerializationException
	 * @return string
	 */
	function readString() ;
}

?>