<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "extbase_realurl".
 *
 * Auto generated 18-04-2014 23:31
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
	'version' => '0.9.4',
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
	'_md5_values_when_last_written' => 'a:16:{s:13:"composer.json";s:4:"2706";s:12:"ext_icon.gif";s:4:"68b4";s:17:"ext_localconf.php";s:4:"a9af";s:10:"LICENSE.md";s:4:"c813";s:9:"README.md";s:4:"77c5";s:22:"Build/ImportSchema.sql";s:4:"9838";s:28:"Build/LocalConfiguration.php";s:4:"c834";s:23:"Build/PackageStates.php";s:4:"7e79";s:38:"Classes/AutoConfigurationGenerator.php";s:4:"0e06";s:39:"Classes/RoutableControllerInterface.php";s:4:"c110";s:29:"Classes/RoutingAnnotation.php";s:4:"b4c5";s:28:"Classes/RoutingException.php";s:4:"7ca3";s:33:"Classes/SegmentValueProcessor.php";s:4:"2263";s:33:"Classes/Routing/PackageRouter.php";s:4:"7840";s:29:"Classes/Rule/AbstractRule.php";s:4:"c163";s:29:"Classes/Rule/RedirectRule.php";s:4:"eccf";}',
);

?>