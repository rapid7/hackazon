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
 * @package gwtphp.lang
 */

define('INNER_JAVA_CLASS_SEPARATOR','$');
define('INNER_PHP_CLASS_SEPARATOR','___');

//TODO: move this class to gwtphp.util package
class JavaSignatureUtil {

	/**
	 *
	 * @var String[]
	 */

	private static $nativeSignatures = array(
		'java.lang.Boolean' => '476441737',
		'java.lang.Byte' => '1571082439',
		'java.lang.Character' => '2663399736',
		'java.lang.Short' => '551743396',
		'java.lang.Integer' => '3438268394',
		'java.lang.Long' => '4227064769',
		'java.lang.Float' => '1718559123',
		'java.lang.Double' => '858496421',
		'java.lang.String' => '2004016611',
		'java.lang.Void' => '2139725816',
		'java.util.Date' => '1659716317',
		'java.util.List' => '536131481',
		'java.util.Map' => '1900819972',
		'java.util.HashMap' => '1797211028',
		'java.util.Set' => '83212643',
		'java.util.HashSet' => '1594477813',
		'java.util.Vector' => '3125574444'
	);
	/**
	 * 
	 *
	 * @param string $signature
	 * @return boolean
	 */
	public static function isPrimitive($signature) {

		switch ($signature) {
			case  TypeSignatures::$BOOLEAN:
			case  TypeSignatures::$BYTE:
			case  TypeSignatures::$CHAR:
			case  TypeSignatures::$DOUBLE:
			case  TypeSignatures::$FLOAT:
			case  TypeSignatures::$INT:
			case  TypeSignatures::$LONG:
			case  TypeSignatures::$SHORT:
				return true;
		}
		return false;
	}

	/**
	 * 
	 *
	 * @param string $signature
	 * @return boolean
	 */
	public static function signatureToName($signature) {

		switch ($signature) {
			case  TypeSignatures::$BOOLEAN: return 'boolean';
			case  TypeSignatures::$BYTE: return 'byte';
			case  TypeSignatures::$CHAR: return 'char';
			case  TypeSignatures::$DOUBLE: return 'double';
			case  TypeSignatures::$FLOAT: return 'float';
			case  TypeSignatures::$INT: return 'int';
			case  TypeSignatures::$LONG: return 'long';
			case  TypeSignatures::$SHORT: return 'short';
			default: throw new Exception('Not a primitive signature: '.$signature);
		}
		return false;
	}


	public static function isNative($className) {
		if (isset(JavaSignatureUtil::$nativeSignatures[$className])) {
			return true;
		}
		else return false;
	}
	/**
	 * example input: java.lang.Date 
	 * output: 1659716317
	 * 
	 *
	 * @param String $className
	 * @return String
	 */
	public static function getSerializationSignatureForNative($className) {
		if (isset(JavaSignatureUtil::$nativeSignatures[$className])) {
			return JavaSignatureUtil::$nativeSignatures[$className];
		}
		else return false;
	}


	/**
	 * @param string $signature
	 * @return boolean
	 */
	public static function isArray($signature) {
		return ($signature[0] == TypeSignatures::$ARRAY) ? true : false;
	}

	/**
	 * @param string $signature
	 * @return boolean
	 */
	public static function isVoid($signature) {
		return ($signature[0] == TypeSignatures::$VOID) ? true : false;
	}

	/**
	 * 
	 *
	 * @param string $signature
	 * @throws SignatureParseException
	 * @return string
	 */
	public static function getSignatureForComponentTypeOfArray($signature) {
		if (!JavaSignatureUtil::isArray($signature)) {
			require_once(GWTPHP_DIR.'/maps/java/lang/SignatureParseException.class.php');
			throw new SignatureParseException("Not an array signature: " . $signature);
		}
		//$second = $signature[1] ;
		if ($signature[1] == TypeSignatures::$OBJECT)	// [Ljava.lang.String;
		return substr($signature,-strlen($signature)+2,strlen($signature)-3);
		else
		return substr($signature,-strlen($signature)+1);
	}

	/**
	 * @param string $signature
	 * @return boolean
	 * @throws SignatureParseException
	 */
	public static function isGeneric($signature) {
		//strrpos
		$lCount = substr_count($signature, '<');
		$rCount = substr_count($signature, '>');
		if ($lCount == $rCount) {
			if ($lCount == 0)
			return false;
			else
			return true;
		}
		else {
			require_once(GWTPHP_DIR.'/maps/java/lang/SignatureParseException.class.php');
			throw new SignatureParseException("Generic signature parsing error (not equal counts of '<' and '>' : " . $signature);
		}
		//return ( strpos($signature,'<') === false ) ? false : true;
	}

	public static function isInnerJavaClass($signature) {
		return	( strpos($className,INNER_JAVA_CLASS_SEPARATOR) !== false) ;
	}

	public static function isInnerPHPClass($signature) {
		return	( strpos($className,INNER_PHP_CLASS_SEPARATOR) !== false) ;
	}

	public static function innecPHPClassNameToJavaClassName($className) {
		return str_replace(INNER_PHP_CLASS_SEPARATOR,INNER_JAVA_CLASS_SEPARATOR,$className);
	}
	public static function innecJavaClassNameToPHPClassName($className) {
		return str_replace(INNER_JAVA_CLASS_SEPARATOR,INNER_PHP_CLASS_SEPARATOR,$className);
	}
	/**
	 * 
	 *
	 * @param string $signature
	 * @throws SignatureParseException
	 * @return string
	 */
	public static function getSignaturesForGenericTypeParameters($signature) {
		$lPos = strpos($signature,'<');
		$rPos = strrpos($signature,'>');

		if ($lPos === false ||$rPos === false || !JavaSignatureUtil::isGeneric($signature)) {
			require_once(GWTPHP_DIR.'/maps/java/lang/SignatureParseException.class.php');
			throw new SignatureParseException("Not an generic signature: " . $signature);
		}

		$types = explode(',',substr($signature,$lPos+1,$rPos-$lPos-1));
		foreach ($types as $key => $type) {
			$types[$key] = trim($type);
		}

		//$genericTypeSignature =
		return $types;

	}

	/**
	 * 
	 *
	 * @param string $signature
	 * @throws SignatureParseException
	 * @return string
	 */
	public static function getSignatureForGenericType($signature) {
		$lPos = strpos($signature,'<');
		$rPos = strrpos($signature,'>');

		if ($lPos === false ||$rPos === false || !JavaSignatureUtil::isGeneric($signature)) {
			require_once(GWTPHP_DIR.'/maps/java/lang/SignatureParseException.class.php');
			throw new SignatureParseException("Not an generic signature: " . $signature);
		}


		//$genericTypeSignature = substr ( $signature, 0, $lPos ).substr ( $signature, $rPos+1, strlen($signature) );
		return substr($signature,0,$lPos).substr ( $signature, $rPos+1, strlen($signature) );


		//$second = $signature[1] ;
		//if ($signature[1] == TypeSignatures::$OBJECT)	// [Ljava.lang.String;
		//	return substr($signature,-strlen($signature)+2,strlen($signature)-3);
		//else
		//	return substr($signature,-strlen($signature)+1);
	}
	//public static function getSerializationSignature(String $signature) {

	//}
}

?>