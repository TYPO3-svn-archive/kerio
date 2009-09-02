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


$LANG->includeLLFile('EXT:kerio/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Kerio Log Viewer' for the 'kerio' extension.
 *
 * @author	Søren Malling <soren.malling@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_kerio
 */
class  tx_kerio_module1 extends t3lib_SCbase {
				var $pageinfo;
				var $logEntries;
				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'list' => $LANG->getLL('func_list'),
							'createconf' => $LANG->getLL('func_createconf'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('bigDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';
						$headerSection = '';
						if($this->MOD_SETTINGS['function'] == 'list') {
							$searchForm['sword'] = '<input type="text" name="SET[sword]" />';
							$searchForm['submit'] = '<input type="submit" name="SET[submit]" value="'.$GLOBALS['LANG']->getLL('form_submit').'" />';
							$headerSection = $this->doc->menuTable (
								array (
									array($GLOBALS['LANG']->getLL('form_sword'), $searchForm['sword']),
									array('', $searchForm['submit'])

								)
							);
						}
						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					switch((string)$this->MOD_SETTINGS['function'])	{
						case "list":
							$this->getLogEntries();
							$content = $this->renderLogEntries();
							$this->content.=$this->doc->section('Message #1:',$content,0,1);
						break;
						case "createconf":
							$content = $this->getLogSearchForm();
							$content .= $this->getLogSearchResult();
							$this->content.=$this->doc->section('Find log by email:',$content,0,1,1);
						break;
					}
				}

				function getLogEntries() {
					$this->logEntries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid,tstamp,message,feuid','tx_kerio_log','deleted=0');
					return $this->logEntries;
				}
				
				function getLogSearchForm() {
					$output = '<input name="SET[tx_kerio_email]" type="text" />';
					return $output;
				}

				function getLogSearchResult() {
					return false;
				}

				function renderLogEntries() {
					$tableLayout = array (
						'table' => array ('<table border="0" cellspacing="1" cellpadding="2" style="width:740px;">', '</table>'),
					    '0' => array (
					    	'tr' => array('<tr class="bgColor2" valign="top">','</tr>'),
					    ),
					    'defRow' => array (
					    	'tr' => array('<tr class="bgColor-20">','</tr>'),
					        '1' => array('<td align="center">','</td>'),
					        'defCol' => array('<td>','</td>'),
					    )
					);

					$table = array();
					$tr = 0;

					$table[$tr][] = $GLOBALS['LANG']->getLL('log_id');
					$table[$tr][] = $GLOBALS['LANG']->getLL('log_timestamp');
					$table[$tr][] = $GLOBALS['LANG']->getLL('log_message');
					$table[$tr][] = $GLOBALS['LANG']->getLL('log_user');

					foreach($this->logEntries as $entry) {
						$tr++;
						$table[$tr][] = $entry['uid'];
						$table[$tr][] = $entry['tstamp'];
						$table[$tr][] = $entry['message'];
						$table[$tr][] = $entry['feuid'];
					}

					$logEntries = $this->doc->table($table, $tableLayout);

					return $logEntries;
				}
				
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kerio/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kerio/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_kerio_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
