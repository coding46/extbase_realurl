<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Note: simply ignored if realurl is not installed. If realurl is not installed, rules on pages will not work but
// the hook below - the direct calling of controllers through plugins (routing) - will still run without realurl.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['extbase_realurl'] =
	'Tx_ExtbaseRealurl_AutoConfigurationGenerator->buildAutomaticRules';

// Note: although using this hook might appear ever so slightly, completely abusive, it is located in the ideal spot:
// right after every environment variable AND the current user (which carries caches needed by Fluid, among others)
// but right before even attempting to look for a page. This gives optimal response times and avoids initializing a
// lot of page resolving measures which are particularly heavy on the system when realurl is installed.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['initFEuser']['extbase_realurl'] =
	'Tx_ExtbaseRealurl_Routing_PackageRouter->attemptRouting';
