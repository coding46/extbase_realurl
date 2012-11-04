<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extbase Realurl: Automatic Realurl configuration for Extbase plugins',
	'description' => 'Creates automatic Realurl rules for all plugins used on the TYPO3 site. Enables extension developers to use class and method annotations to modify the rules that are generated. Supports all versions of Extbase.',
	'category' => 'misc',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
	'shy' => '',
	'dependencies' => 'cms,extbase,realurl',
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
	'version' => '0.9.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
			'cms' => '',
			'extbase' => '',
			'realurl' => '1.12.3-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => 'a:9:{s:20:"class.tx_realurl.php";s:4:"9691";s:12:"ext_icon.gif";s:4:"68b4";s:17:"ext_localconf.php";s:4:"eb18";s:9:"README.md";s:4:"9b90";s:38:"Classes/AutoConfigurationGenerator.php";s:4:"dc9f";s:39:"Classes/RoutableControllerInterface.php";s:4:"ed1c";s:29:"Classes/RoutingAnnotation.php";s:4:"50ff";s:28:"Classes/RoutingException.php";s:4:"7ca3";s:33:"Classes/SegmentValueProcessor.php";s:4:"4a62";}',
);

?>