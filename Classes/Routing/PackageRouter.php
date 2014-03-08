<?php
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class Tx_ExtbaseRealurl_Routing_PackageRouter {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $temporaryFile;

	/**
	 * Constructor
	 */
	public function __construct() {
		$contentObject = new \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer();
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->objectManager->get('TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface')
			->setContentObject($contentObject);
		$hash = sha1($_SERVER['REQUEST_URI']);
		$this->temporaryFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3temp/extbase_realurl_' . $hash . '.tmp');
	}

	/**
	 * Attempts to route a request to a controller by parsing the raw
	 * request path segments, mapping them to an Extbase Request and
	 * attempting to dispatch that request.
	 *
	 * @param TypoScriptFrontendController $reference
	 * @param array $parameters
	 * @return void
	 */
	public function attemptRouting(array $parameters, TypoScriptFrontendController $reference) {
		\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($parameters);
		$segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
		if (3 > count($segments)) {
			// segment count does not satisfy minimum of vendor + extension + plugin
			return;
		}
		$this->outputIfTempFileExists();
		/** @var TypoScriptFrontendController $controller */
		$controller = reset($parameters);
		list ($vendor, $extensionKey, $plugin, $controllerName, $action, $format, $preventCache) = $this->resolveRequestParameters($segments);
		if (FALSE === \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extensionKey)) {
			// desired extension not loaded; let pass (likely giving a 404 result; depends on site configuration)
			$controller->pageNotFoundAndExit('Package "' . $extensionKey . '" is not loaded');
		}
		$request = $this->createRequest($vendor, $extensionKey, $plugin, $controllerName, $action, $format);
		$request = $this->applyArgumentsFromSegmentsToRequest($request, (array) $segments);
		$request = $this->applyArgumentsFromRequestToRequest($request);
		$content = $this->dispatchRequest($request);
		$this->output($content, $preventCache);
	}

	/**
	 * @return void
	 */
	protected function outputIfTempFileExists() {
		if (TRUE === file_exists($this->temporaryFile)) {
			$content = file_get_contents($this->temporaryFile);
			$this->output($content);
		}
	}

	/**
	 * @param string $content
	 * @param boolean $preventCache
	 * @return void
	 */
	protected function output($content, $preventCache = FALSE) {
		if ((time() - 3600) > filectime($this->temporaryFile)) {
			unlink($this->temporaryFile);
		}
		if (FALSE === $preventCache) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($this->temporaryFile, $content);
		}
		echo $content;
		exit();
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Request $request
	 * @return string
	 */
	protected function dispatchRequest(\TYPO3\CMS\Extbase\Mvc\Request $request) {
		/** @var \TYPO3\CMS\Extbase\Mvc\Web\Response $response */
		$response = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Response');
		$dispatcher = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Dispatcher');
		$dispatcher->dispatch($request, $response);
		$content = $response->getContent();
		return $content;
	}

	/**
	 * @param array $segments
	 * @return array
	 */
	protected function resolveRequestParameters(array &$segments) {
		$possibleFormat = pathinfo(end($segments), PATHINFO_EXTENSION);
		$argumentSeparator = strpos($possibleFormat, '?');
		$possibleFormat = FALSE !== $argumentSeparator ? substr($possibleFormat, 0, $argumentSeparator) : $possibleFormat;
		if (NULL === $possibleFormat) {
			$format = 'html';
		} else {
			$segments[count($segments) - 1] = pathinfo($segments[count($segments) - 1], PATHINFO_FILENAME);
			$format = $possibleFormat;
		}
		$vendor = array_shift($segments);
		$package = array_shift($segments);
		$plugin = array_shift($segments);
		if (1 === preg_match('/[A-Z]/', reset($segments))) {
			$controller = array_shift($segments);
		} elseif (TRUE === isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$package]['plugins'][$plugin]['controllers'])) {
			$controller = key($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$package]['plugins'][$plugin]['controllers']);
		} else {
			$controller = 'Standard';
		}
		// if a user is logged in, no caching can occur. As a safety measure; given that caching happens based on
		// the request URI which could be the same for multiple users.
		$action = NULL;
		if (1 === count($segments) % 2) {
			$action = array_shift($segments);
			$argumentSeparator = strpos($action, '?');
			$action = FALSE !== $argumentSeparator ? substr($action, 0, $argumentSeparator) : $action;
		}
		$preventCache = FALSE !== $GLOBALS['TSFE']->fe_user->user;
		if (TRUE === isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$package]['plugins'][$plugin]['controllers'][$controller])) {
			$actionSet = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase']['extensions'][$package]['plugins'][$plugin]['controllers'][$controller];
			$preventCache = in_array($action, $actionSet['nonCacheableActions']);
		} else {
			$actionSet = array(
				'actions' => array('index'),
				'nonCacheableActions' => array()
			);
		}
		if (NULL === $action) {
			$action = reset($actionSet['actions']);
		}
		$extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($package);
		return array($vendor, $extensionKey, $plugin, $controller, $action, $format, $preventCache);
	}

	/**
	 * @param string $vendor
	 * @param string $package
	 * @param string $plugin
	 * @param string $controller
	 * @param string $action
	 * @param string $format
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	protected function createRequest($vendor, $package, $plugin, $controller, $action, $format) {
		/** @var \TYPO3\CMS\Extbase\Mvc\Web\Request $request */
		$request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Request');
		$request->setControllerVendorName($vendor);
		$request->setControllerExtensionName($package);
		$request->setPluginName($plugin);
		$request->setControllerName($controller);
		$request->setControllerActionName($action);
		$request->setFormat($format);
		return $request;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Request $request
	 * @param array $segments
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	protected function applyArgumentsFromSegmentsToRequest(\TYPO3\CMS\Extbase\Mvc\Request $request, array $segments) {
		$argumentName = NULL;
		foreach ($segments as $segment) {
			if (NULL === $argumentName) {
				$argumentName = $segment;
			} else {
				$argumentValue = urldecode($segment);
				$request->setArgument($argumentName, $argumentValue);
			}
		}
		return $request;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\Request $request
	 * @return \TYPO3\CMS\Extbase\Mvc\Web\Request
	 */
	protected function applyArgumentsFromRequestToRequest(\TYPO3\CMS\Extbase\Mvc\Request $request) {
		foreach ($_REQUEST as $argumentName => $argumentValue) {
			$request->setArgument($argumentName, $argumentValue);
		}
		return $request;
	}

}
