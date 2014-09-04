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
 * @package gwtphp.util
 */

/**
 * Used to manage character
 *
 */
class CharacterUtil {
	
	/**
	 * converts char to unicode
	 *
	 * @param int $codes
	 * @return string
	 */
	public static function  chrUTF8 ($codes) {
		if (is_scalar($codes)) $codes= func_get_args();
		$str= '';
		foreach ($codes as $code) $str.= html_entity_decode('&#'.$code.';',ENT_NOQUOTES,'UTF-8');
		return $str;
	}

	/**
	 * converts first char from string $c to integer
	 *
	 * @param string $c
	 * @param int $index
	 * @param int $bytes
	 * @return boolean - true succesed / failure
	 */
	public static function ordUTF8($c, $index = 0, &$bytes = null)
	{
		$len = strlen($c);
		$bytes = 0;

		if ($index >= $len)
		return false;

		$h = ord($c{$index});

		if ($h <= 0x7F) {
			$bytes = 1;
			return $h;
		}
		else if ($h < 0xC2)
		return false;
		else if ($h <= 0xDF && $index < $len - 1) {
			$bytes = 2;
			return ($h & 0x1F) <<  6 | (ord($c{$index + 1}) & 0x3F);
		}
		else if (($h <= 0xEF) && ($index < $len - 2)) {
			$bytes = 3;
			return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6
			| (ord($c{$index + 2}) & 0x3F);
		}
		else if ($h <= 0xF4 && $index < $len - 3) {
			$bytes = 4;
			return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12
			| (ord($c{$index + 2}) & 0x3F) << 6
			| (ord($c{$index + 3}) & 0x3F);
		}
		else
		return false;
	}
}


?>