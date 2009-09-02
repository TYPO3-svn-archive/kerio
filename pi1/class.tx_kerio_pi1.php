<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Søren Malling <soren.malling@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_tslib.'class.tslib_content.php');
require_once(PATH_t3lib.'class.t3lib_div.php');


/**
 * Plugin 'E-mail administration' for the 'kerio' extension.
 *
 * @author	Søren Malling <soren.malling@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_kerio
 */
class tx_kerio_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_kerio_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_kerio_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'kerio';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
	  $this->cObj = t3lib_div::makeInstance("tslib_cObj");
		$this->conf = $conf;
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm();
		
		$this->checkAndMergeConfiguration();
		
		print_r($this->conf);
		print_r($GLOBALS['TYPO3_CONF_VARS']);
		
		return $this->pi_wrapInBaseClass($content);
	}





  function checkAndMergeConfiguration() {
    if(!is_array(unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]))) {
      $this->conf = $this->conf;
    } else {
      
      $this->conf = t3lib_div::array_merge($this->conf, unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]));
    }
  }



	function getMaillistMembers($maillist) {
		$members = file_get_contents("http://mail.fdf.dk/maillists/" . $maillist . "/members");
#		$this->maillist[$maillist]['moderators'] = 
	}

	function getMaillistModerators($maillist) {

	}



	function displayRequestForm() {
		$content = "please confirm your email for the domain ".$this->pi_getFFvalue($this->cObj->data['pi_flexform'], "kerio_domain");
		$content .= '<form action="" method="POST"><inupt type="hidden" name="cmd" value="create"/> <input type="submit" name="button" value="Send confirmation" />';
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kerio/pi1/class.tx_kerio_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kerio/pi1/class.tx_kerio_pi1.php']);
}

?>
