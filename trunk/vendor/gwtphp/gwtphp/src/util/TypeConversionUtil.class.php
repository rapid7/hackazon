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

class TypeConversionUtil {
	static $TWO_TO_63;
	static $TWO_TO_64;

	public static function dec2hex($dec) {
		$hex = ($dec == 0 ? '0' : '');
		
		if ($dec < 0) {
			$dec = - $dec;
			$sign = '-';
		} else {
			$sign = '';
		}
		while ( $dec > 0 ) {
			$hex = dechex ( $dec - floor ( $dec / 16 ) * 16 ) . $hex;
			$dec = floor ( $dec / 16 );
		}
		return $sign . $hex;
	}
	public static function hex2dec($hex) {
		$dec = hexdec ( $hex );
		if ($dec != 0 && $hex [0] == '-') {
			$dec = - $dec;
		}
		return $dec;
	}
	
	/**
	 * Function for processing Long as of GWT Protocol version 6
	 */
	public static function base64toDec($base64){
		$digits = strlen($base64)-1;
		$multiplier = '1';
		$result = '0';
		while ($digits >= 0){
			$result = bcadd($result, bcmul(strval(self::base64toDecSingle($base64[$digits])), $multiplier));
			$multiplier = bcmul($multiplier, '64');
			$digits--;
		}
		if (bccomp($result, self::$TWO_TO_63)==1){	//wrap around
			return (float)(bcsub($result, self::$TWO_TO_64));
		}
		return $result;
	}
	public static function dectoBase64($dec){
		$base64 = ($dec == 0 ? 'A' : '');
		
		if ($dec < 0) {
			// encode it as this number such that when it wraps on client
			// side, the proper negative number will be revealed
			$dec = bcadd(self::$TWO_TO_64, sprintf('%0.0f',($dec)));
		}else{
			// change to arbitrary-precision string
			$dec = sprintf('%0.0f',($dec));
		}
		while ( $dec > 0 ) {
			$modulus = bcmod($dec, '64');
			$base64 = self::dectoBase64Single ( (int)$modulus ) . $base64;
			$dec = bcdiv(bcsub($dec, $modulus), '64');
		}
		
		// use single quotes to wrap the string, according to GWT specs
		return "'" . $base64 . "'";
	}
	
	/**
	 * GWT-style base64 conversion (using $ for 62 and _ for 63)
	 */
	private static function base64toDecSingle($base64){
		$ascii = ord($base64);
		if ($ascii>=65 && $ascii<=90){	//'A'-'Z'
			return ($ascii-65);
		}
		if ($ascii>=97 && $ascii<=122){	//'a'-'z'
			return ($ascii-97+26);
		}
		if ($ascii>=48 && $ascii<=57){	//'0'-'9'
			return ($ascii-48+52);
		}
		if ($ascii==36) return 62;		//'$'
		if ($ascii==95) return 63;		//'_'
		
		return 0;
	}
	private static function dectoBase64Single($base64){
		if ($base64>=0 && $base64<=25){
			return chr($base64+65);
		}
		if ($base64>=26 && $base64<=51){
			return chr($base64+97-26);
		}
		if ($base64>=52 && $base64<=61){
			return chr($base64+48-52);
		}
		if ($base64==62) return '$';
		if ($base64==63) return '_';
		
		return 'A';
	}
	
	/**
	 * @param string $v
	 * @return boolean
	 */
	public static function parseBoolean($v) {
		return ( boolean ) $v;
	}
	/**
	 * @param string $v
	 * @return byte (int)
	 */
	public static function parseByte($v) {
		return intval ( $v ); // there are not type byte in php
	}
	/**
	 * @param string $v
	 * @return char (int)
	 */
	public static function parseChar($v) {
		return intval ( $v ); // there are not type byte in php
	}
	
	/**
	 * accepts NaN, Infinity, -Infinity
	 * @param string $v
	 * @return double
	 */
	public static function parseDouble($v) {
		switch ($v) {
			case 'NaN' :
				return NAN;
			case 'Infinity' :
				return INF;
			case '-Infinity' :
				return - INF;
			default :
				return doubleval ( $v );
		}
	}
	/**
	 * accepts NaN, Infinity, -Infinity
	 * @param string $v
	 * @return double
	 */
	public static function parseFloat($v) {
		switch ($v) {
			case 'NaN' :
				return NAN;
			case 'Infinity' :
				return INF;
			case '-Infinity' :
				return - INF;
			default :
				return floatval ( $v );
		}
	}
	/**
	 * @param string $v
	 * @return int
	 */
	public static function parseInt($v) {
		return intval ( $v );
	}
	/** 
	 * for large values we make long as double.
	 * @param string $v
	 * @return long (double)
	 */
	public static function parseLong($v) {
		return doubleval ( $v );
	}
	/** 
	 * @param string $v
	 * @return short (int) 
	 */
	public static function parseShort($v) {
		return intval ( $v );
	}
}

// workaround for initializing static variable
TypeConversionUtil::$TWO_TO_63 = bcpow('2', '63');
TypeConversionUtil::$TWO_TO_64 = bcpow('2', '64');

?>