<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * Amf dates will be converted to and from this class. The PHP DateTime class is for PHP >= 5.2.0, and setTimestamp for PHP >= 5.3.0, so it can't be used in amfPHP
 * Of course feel free to use it yourself if your host supports it.
 * 
 * @package Amfphp_Core_Amf_Types
 * @author Danny Kopping
 */
class Amfphp_Core_Amf_Types_Date
{
        /**
         * number of ms since 1st Jan 1970
         * @var integer
         */
    	public $timeStamp;
        
        /**
         * time stamp
         * @param integer $timeStamp
         */
	public function __construct($timeStamp)
	{
		$this->timeStamp = $timeStamp;

	}
}

?>