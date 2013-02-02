<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['extbase_realurl'] = 'Tx_ExtbaseRealurl_AutoConfigurationGenerator->buildAutomaticRules';

$version = TYPO3_version;
if ($version{0} >= 6) {
	require_once t3lib_extMgm::extPath('extbase_realurl', 'class.tx_realurl.php');
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['tx_realurl'] =
		array('className' => 'ux_tx_realurl');
} else {
	$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/realurl/class.tx_realurl.php'] = t3lib_extMgm::extPath('extbase_realurl', 'class.tx_realurl.php');
}