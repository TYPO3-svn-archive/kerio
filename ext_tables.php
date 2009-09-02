<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$TCA['tx_kerio_log'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:kerio/locallang_db.xml:tx_kerio_log',		
		'label'     => 'message',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY tstamp',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_kerio_log.gif',
	),
);

$tempColumns = array (
	'tx_kerio_email' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:kerio/locallang_db.xml:fe_users.tx_kerio_email',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',	
			'eval' => 'uniqueInPid',
		)
	),
);


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_kerio_email;;;;1-1-1');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';


t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds_pi1.xml');
t3lib_extMgm::addPlugin(array(
	'LLL:EXT:kerio/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,'static/kerio/', 'kerio');


if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txkerioM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
		
	t3lib_extMgm::addModule('tools', 'txkerioM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}
?>
