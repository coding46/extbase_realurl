<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "extbase_realurl".
 *
 * Auto generated 22-02-2013 23:25
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extbase URL handling',
	'description' => 'Makes it possible to call Extbase controllers directly using nice urls - if Realurl is installed, also enables automatic rules for plugins in pages',
	'category' => 'misc',
	'author' => 'Claus Due',
	'author_email' => 'claus@namelesscoder.net',
	'author_company' => '',
	'shy' => '',
	'dependencies' => 'cms,extbase',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.9.2',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.0.0-6.2.99',
			'cms' => '',
			'extbase' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'realurl' => '1.12.3-0.0.0',
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:11:{s:20:"class.tx_realurl.php";s:4:"9691";s:12:"ext_icon.gif";s:4:"68b4";s:17:"ext_localconf.php";s:4:"9d8f";s:9:"README.md";s:4:"9b90";s:38:"Classes/AutoConfigurationGenerator.php";s:4:"6a80";s:39:"Classes/RoutableControllerInterface.php";s:4:"ed1c";s:29:"Classes/RoutingAnnotation.php";s:4:"b4c5";s:28:"Classes/RoutingException.php";s:4:"7ca3";s:33:"Classes/SegmentValueProcessor.php";s:4:"f185";s:29:"Classes/Rule/AbstractRule.php";s:4:"c163";s:29:"Classes/Rule/RedirectRule.php";s:4:"eccf";}',
);

?>