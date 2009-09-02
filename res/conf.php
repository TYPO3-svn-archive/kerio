<?php
if (!defined ('TYPO3_MODE')) {
        die ('Access denied.');
}

$TYPO3_CONF_VARS['EXT']['extConf']['kerio']['serverConf'] = array (
	"host" => "localhost",
	"username" => "username",
	"password" => "password",
	"port" => "389",
	"domain" => "domain.tld"
);

?>
