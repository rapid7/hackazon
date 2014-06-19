<?php

/*
 * 
 *  SanitizationLevel
 *  		<option value="none">No sanitization</option>
 *		<option value="whitelist" >Accept Only Whitelisted Items</option>
 *		<option value="reject_low" >Case-Sensitively Reject Blacklisted Items</option>
 *		<option value="reject_high" >Case-Insensitively Reject Blacklisted Items</option>
 *		<option value="escape" >Backslash-Escape Blacklisted Items</option>
 *		<option value="low" >Case-Sensitively Remove Blacklisted Items</option>
 *		<option value="medium" >Case-Insensitively Remove Blacklisted Items</option>
 *		<option value="high" >Case-Insensitively and Repetitively Remove Blacklisted Items</option>
 * 
 * 
 *  PatternMatchingStyle
 *              Keywords || Regexes 
 * 
 * 
 *  SanitizationParameters
 *              [comma-separated keywords or regexes to whitelist or blacklist below.]
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */

return array(
	'sql' => array('select', 
                               array(
                                   'Double-up Single Quotes' => true, /*true-false*/
                                   'SanitizationLevel' => 'none',
                                   'PatternMatchingStyle' => 'Keywords',
                                   'SanitizationParameters' => array()
				   )
				),
);
