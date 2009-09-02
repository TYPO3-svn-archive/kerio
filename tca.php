<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_kerio_log'] = array (
	'ctrl' => $TCA['tx_kerio_log']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,message,feuid,timestamp'
	),
	'feInterface' => $TCA['tx_kerio_log']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'message' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:kerio/locallang_db.xml:tx_kerio_log.message',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'feuid' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:kerio/locallang_db.xml:tx_kerio_log.feuid',		
			'config' => array (
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'timestamp' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:kerio/locallang_db.xml:tx_kerio_log.timestamp',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, message, feuid, timestamp')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>