<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2004 Kasper Skaarhoj (kasper@typo3.com)
 *  (c) 2005-2010 Dmitry Dulepov (dmitry@typo3.org)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Class for creating and parsing Speaking Urls
 *
 * $Id$
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Dmitry Dulepov <dmitry@typo3.org>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  107: class tx_realurl
 *
 *              SECTION: Translate parameters to a Speaking URL (t3lib_tstemplate::linkData)
 *  152:     function tx_realurl()
 *  170:     function encodeSpURL(&$params, $ref)
 *  241:     function encodeSpURL_doEncode($inputQuery, $cHashCache = FALSE, $origUrl = '')
 *  324:     function encodeSpURL_pathFromId(&$paramKeyValues, &$pathParts)
 *  353:     function encodeSpURL_gettingPostVarSets(&$paramKeyValues, &$pathParts, $postVarSetCfg)
 *  390:     function encodeSpURL_fileName(&$paramKeyValues)
 *  412:     function encodeSpURL_setSequence($varSetCfg, &$paramKeyValues, &$pathParts)
 *  516:     function encodeSpURL_setSingle($keyWord, $keyValues, &$paramKeyValues, &$pathParts)
 *  550:     function encodeSpURL_encodeCache($urlToEncode, $internalExtras, $setEncodedURL = '')
 *  617:     function encodeSpURL_cHashCache($newUrl, &$paramKeyValues)
 *
 *              SECTION: Translate a Speaking URL to parameters (tslib_fe)
 *  669:     function decodeSpURL($params, $ref)
 *  759:     function decodeSpURL_checkRedirects($speakingURIpath)
 *  801:     function decodeSpURL_doDecode($speakingURIpath, $cHashCache = FALSE)
 *  877:     function decodeSpURL_createQueryStringParam($paramArr, $prependString = '')
 *  900:     function decodeSpURL_createQueryString(&$getVars)
 *  925:     function decodeSpURL_idFromPath(&$pathParts)
 *  966:     function decodeSpURL_settingPreVars(&$pathParts, $config)
 *  989:     function decodeSpURL_settingPostVarSets(&$pathParts, $postVarSetCfg)
 * 1054:     function decodeSpURL_fixBrackets(&$arr)
 * 1080:     function decodeSpURL_fileName($fileName)
 * 1108:     function decodeSpURL_getSequence(&$pathParts, $setupArr)
 * 1201:     function decodeSpURL_getSingle($keyValues)
 * 1217:     function decodeSpURL_throw404($msg)
 * 1251:     function decodeSpURL_jumpAdmin()
 * 1275:     function decodeSpURL_jumpAdmin_goBackend($pageId)
 * 1290:     function decodeSpURL_decodeCache($speakingURIpath, $cachedInfo = '')
 * 1354:     function decodeSpURL_cHashCache($speakingURIpath)
 *
 *              SECTION: Alias-ID look up functions
 * 1385:     function lookUpTranslation($cfg, $value, $aliasToUid = FALSE)
 * 1495:     function lookUp_uniqAliasToId($cfg, $aliasValue, $onlyNonExpired = FALSE)
 * 1522:     function lookUp_idToUniqAlias($cfg, $idValue, $lang, $aliasValue = '')
 * 1550:     function lookUp_newAlias($cfg, $newAliasValue, $idValue, $lang)
 * 1620:     function lookUp_cleanAlias($cfg, $newAliasValue)
 *
 *              SECTION: General helper functions (both decode/encode)
 * 1665:     function setConfig()
 * 1699:     function getPostVarSetConfig($page_id, $mainCat = 'postVarSets')
 * 1721:     function pageAliasToID($alias)
 * 1744:     function rawurlencodeParam($str)
 * 1759:     function checkCondition($setup, $prevVal, $value)
 * 1777:     function isBEUserLoggedIn()
 *
 *              SECTION: External Hooks
 * 1794:     function clearPageCacheMgm($params, $ref)
 * 1809:     function isString(&$str, $paramName)
 * 1834:     function findRootPageId($host = '')
 *
 * TOTAL FUNCTIONS: 41
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class for creating and parsing Speaking Urls
 * This class interfaces with hooks in TYPO3 inside tslib_fe (for parsing speaking URLs to GET parameters) and in t3lib_tstemplate (for parsing GET parameters into a speaking URL)
 *
 * @author	Claus Due <claus@wildside.dk>
 * @package ExtbaseRealurl
 */
class ux_tx_realurl extends tx_realurl {

	/**
	 * Traverses a set of GETvars configured (array of segments)
	 *
	 * @param	array		Array of segment-configurations.
	 * @param	array		Current URLs GETvar => value pairs in array, being translated into pathParts, continously shortend. Passed by reference.
	 * @param	array		Numerical array of path-parts, continously being filled. Passed by reference.
	 * @return	void		Removing values from $paramKeyValues / Setting values in $pathParts
	 * @see encodeSpURL_doEncode(), encodeSpURL_gettingPostVarSets(), decodeSpURL_getSequence()
	 */
	protected function encodeSpURL_setSequence($varSetCfg, &$paramKeyValues, &$pathParts) {

		// Traverse array of segments configuration
		$prevVal = '';
		if (is_array($varSetCfg)) {
			foreach ($varSetCfg as $setup) {
				switch ($setup['type']) {
					case 'action':
						$pathPartVal = '';

						// Look for admin jump:
						if ($this->adminJumpSet) {
							foreach ($setup['index'] as $pKey => $pCfg) {
								if ((string)$pCfg['type'] == 'admin') {
									$pathPartVal = $pKey;
									$this->adminJumpSet = FALSE;
									break;
								}
							}
						}

						// Look for frontend user login:
						if ($this->fe_user_prefix_set) {
							foreach ($setup['index'] as $pKey => $pCfg) {
								if ((string)$pCfg['type'] == 'feLogin') {
									$pathPartVal = $pKey;
									$this->fe_user_prefix_set = FALSE;
									break;
								}
							}
						}

						// If either pathPartVal has been set OR if _DEFAULT type is not bypass, set a value:
						if (strlen($pathPartVal) || $setup['index']['_DEFAULT']['type'] != 'bypass') {

							// If admin jump did not set $pathPartVal, look for first pass-through (no "type" set):
							if (!strlen($pathPartVal)) {
								foreach ($setup['index'] as $pKey => $pCfg) {
									if (!strlen($pCfg['type'])) {
										$pathPartVal = $pKey;
										break;
									}
								}
							}

							// Setting part of path:
							$pathParts[] = rawurlencode(strlen($pathPartVal) ? $pathPartVal : $this->NA);
						}
						break;
					default:
						if (!is_array($setup['cond']) || $this->checkCondition($setup['cond'], $prevVal)) {

							// Looking if the GET var is found in parameter index
							$GETvar = $setup['GETvar'];
							if ($GETvar == $this->ignoreGETvar) {
								// Do not do anything with this var!
								continue;
							}
							$parameterSet = isset($paramKeyValues[$GETvar]);
							$GETvarVal = $parameterSet ? $paramKeyValues[$GETvar] : '';

							// Set reverse map:
							$revMap = is_array($setup['valueMap']) ? array_flip($setup['valueMap']) : array();

							if (isset($revMap[$GETvarVal])) {
								$prevVal = $GETvarVal;
								$pathParts[] = rawurlencode($revMap[$GETvarVal]);
								$this->cHashParameters[$GETvar] = $GETvarVal;
							} elseif ($setup['noMatch'] == 'bypass') {
								// If no match in reverse value map and "bypass" is set, remove the parameter from the URL
								// Must rebuild cHash because we remove a parameter!
								$this->rebuildCHash |= $parameterSet;
							} elseif ($setup['noMatch'] == 'null') {
								// If no match and "null" is set, then set "dummy" value
								// Set "dummy" value (?)
								$prevVal = '';
								$pathParts[] = '';
								$this->rebuildCHash |= $parameterSet;
							} elseif ($setup['userFunc']) {
								$params = array(
									'pObj' => &$this,
									'value' => $GETvarVal,
									'decodeAlias' => false,
									'pathParts' => &$pathParts,
									'setup' => $setup
								);
								$prevVal = $GETvarVal;
								$GETvarVal = t3lib_div::callUserFunction($setup['userFunc'], $params, $this);
								$pathParts[] = rawurlencode($GETvarVal);
								$this->cHashParameters[$GETvar] = $prevVal;
							} elseif (is_array($setup['lookUpTable'])) {
								$prevVal = $GETvarVal;
								$GETvarVal = $this->lookUpTranslation($setup['lookUpTable'], $GETvarVal);
								$pathParts[] = rawurlencode($GETvarVal);
								$this->cHashParameters[$GETvar] = $prevVal;
							} elseif (isset($setup['valueDefault'])) {
								$prevVal = $setup['valueDefault'];
								$pathParts[] = rawurlencode($setup['valueDefault']);
								$this->cHashParameters[$GETvar] = $setup['valueMap'][$setup['valueDefault']];
								$this->rebuildCHash |= !$parameterSet;
							} else {
								$prevVal = $GETvarVal;
								$pathParts[] = rawurlencode($GETvarVal);
								$this->cHashParameters[$GETvar] = $prevVal;
								$this->rebuildCHash |= !$parameterSet;
							}

							// Finally, unset GET var so it doesn't get processed once more:
							unset($paramKeyValues[$setup['GETvar']]);
						}
						break;
				}
			}
		}
	}

	/**
	 * Pulling variables of the path parts
	 *
	 * @param	array		Parts of path. NOTICE: Passed by reference.
	 * @param	array		Setup array for segments in set.
	 * @return	string		GET parameter string
	 * @see decodeSpURL_settingPreVars(), decodeSpURL_settingPostVarSets()
	 */
	protected function decodeSpURL_getSequence(&$pathParts, $setupArr) {
		$GET_string = '';
		$prevVal = '';
		foreach ($setupArr as $setup) {
			if (count($pathParts) == 0) {
				// If we are here, it means we are at the end of the URL.
				// Since some items still remain in the $setupArr, it means
				// we stripped empty segments at the end of the URL on encoding.
				// Reconstruct them or cHash check will fail in TSFE.
				// Related to bugs #15906, #18477.
				if (!$setup['optional'] && $setup['noMatch'] != 'bypass') {
					if (!isset($_GET[$setup['GETvar']]) && (!is_array($setup['cond']) || $this->checkCondition($setup['cond'], $prevVal))) {
						$GET_string .= '&' . rawurlencode($setup['GETvar']) . '=';
						$prevVal = '';
					}
				}
			}
			else {
				// Get value and remove from path parts:
				$value = $origValue = array_shift($pathParts);
				$value = rawurldecode($value);

				switch ($setup['type']) {
					case 'action':
						// Find index key:
						$idx = isset($setup['index'][$value]) ? $value : '_DEFAULT';

						// Look up type:
						switch ((string)$setup['index'][$idx]['type']) {
							case 'redirect':
								$url = (string)$setup['index'][$idx]['url'];
								$url = str_replace('###INDEX###', rawurlencode($value), $url);
								$this->appendFilePart($pathParts);
								$remainPath = implode('/', $pathParts);
								if ($this->appendedSlash) {
									$remainPath = substr($remainPath, 0, -1);
								}
								$url = str_replace('###REMAIN_PATH###', rawurlencode(rawurldecode($remainPath)), $url);

								header('Location: ' . t3lib_div::locationHeaderUrl($url));
								exit();
								break;
							case 'admin':
								$this->decodeSpURL_jumpAdmin();
								break;
							case 'notfound':
								$this->decodeSpURL_throw404('A required value from "' . @implode(',', @array_keys($setup['match'])) . '" of path was not matching "' . $value . '" which was actually found.');
								break;
							case 'bypass':
								array_unshift($pathParts, $origValue);
								break;
							case 'feLogin':
								// Do nothing.
								break;
						}
						break;
					default:
						if (!is_array($setup['cond']) || $this->checkCondition($setup['cond'], $prevVal)) {

							// Map value if applicable:
							if (isset($setup['valueMap'][$value])) {
								$value = $setup['valueMap'][$value];
							} elseif ($setup['noMatch'] == 'bypass') {
								// If no match and "bypass" is set, then return the value to $pathParts and break
								array_unshift($pathParts, $origValue);
								break;
							} elseif ($setup['noMatch'] == 'null') { // If no match and "null" is set, then break (without setting any value!)
								break;
							} elseif ($setup['userFunc']) {
								$params = array(
									'decodeAlias' => true,
									'origValue' => $origValue,
									'pathParts' => &$pathParts,
									'pObj' => &$this,
									'value' => $value,
									'setup' => $setup
								);
								$value = t3lib_div::callUserFunction($setup['userFunc'], $params, $this);
							} elseif (is_array($setup['lookUpTable'])) {
								$temp = $value;
								$value = $this->lookUpTranslation($setup['lookUpTable'], $value, TRUE);
								if ($setup['lookUpTable']['enable404forInvalidAlias'] && !self::testInt($value) && !strcmp($value, $temp)) {
									$this->decodeSpURL_throw404('Couldn\'t map alias "' . $value . '" to an ID');
								}
							} elseif (isset($setup['valueDefault'])) { // If no matching value and a default value is given, set that:
								$value = $setup['valueDefault'];
							}

							// Set previous value:
							$prevVal = $value;

							// Add to GET string:
							if ($setup['GETvar'] && strlen($value)) { // Checking length of value; normally a *blank* parameter is not found in the URL! And if we don't do this we may disturb "cHash" calculations!
								if (isset($this->extConf['init']['emptySegmentValue']) && $this->extConf['init']['emptySegmentValue'] === $value) {
									$value = '';
								}
								$GET_string .= '&' . rawurlencode($setup['GETvar']) . '=' . rawurlencode($value);
							}
						} else {
							array_unshift($pathParts, $origValue);
							break;
						}
						break;
				}
			}
		}

		return $GET_string;
	}

	/**
	 * Tests if the value represents an integer number.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	static public function testInt($value) {
		static $useOldGoodTestInt = null;

		if (is_null($useOldGoodTestInt)) {
			$useOldGoodTestInt = !class_exists('t3lib_utility_Math');
		}
		if ($useOldGoodTestInt) {
			$result = t3lib_div::testInt($value);
		}
		else {
			$result = t3lib_utility_Math::canBeInterpretedAsInteger($value);
		}
		return $result;
	}
}
