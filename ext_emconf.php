<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extbase Realurl: Automatic Realurl configuration for Extbase plugins',
	'description' => 'Creates automatic Realurl rules for all plugins used on the TYPO3 site. Enables extension developers to use class and method annotations to modify the rules that are generated. Supports all versions of Extbase.',
	'category' => 'misc',
	'author' => 'Claus Due',
	'author_email' => 'claus@wildside.dk',
	'author_company' => 'Wildside A/S',
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
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5-0.0.0',
			'cms' => '',
			'extbase' => '',
			'realurl' => '1.12.4-0.0.0'
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
	'_md5_values_when_last_written' => '',
);

?>