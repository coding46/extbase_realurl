<?php
class Tx_ExtbaseRealurl_Rule_RedirectRule extends Tx_ExtbaseRealurl_Rule_AbstractRule {

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * @var integer
	 */
	protected $entryLevel;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var string
	 */
	protected $controller;

	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var string
	 */
	protected $pluginName;

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @var integer
	 */
	protected $status;

	/**
	 * @var array
	 */
	protected $commandToPropertyMap = array(
		'Redirect' => 'method', 'EntryLevel' => 'entryLevel', 'Controller' => 'controller', 'Action' => 'action',
		'PluginName' => 'pluginName', 'ExtensionName' => 'extensionName', 'Status' => 'status', 'Prefix' => 'prefix'
		);

	/**
	 * @param string $annotation
	 */
	public function __construct($annotation) {
		$this->detectArgumentName($annotation);
		$matches = array();
		$matched = preg_match_all($this->commandPattern, $annotation, $matches, PREG_SET_ORDER, 0);
		if ($matched) {
			foreach ($matches as $match) {
				$commandArgument = array_pop($match);
				$commandName = array_pop($match);
				if (isset($this->commandToPropertyMap[$commandName]) === TRUE) {
					$mappedProperty = $this->commandToPropertyMap[$commandName];
					$this->$mappedProperty = $commandArgument;
				}
			}
		}
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function setMethod($method) {
		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string $prefix
	 * @return void
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $controller
	 * @return void
	 */
	public function setController($controller) {
		$this->controller = $controller;
	}

	/**
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * @param integer $entryLevel
	 * @return void
	 */
	public function setEntryLevel($entryLevel) {
		$this->entryLevel = $entryLevel;
	}

	/**
	 * @return integer
	 */
	public function getEntryLevel() {
		return $this->entryLevel;
	}

	/**
	 * @param string $extensionName
	 * @return void
	 */
	public function setExtensionName($extensionName) {
		$this->extensionName = $extensionName;
	}

	/**
	 * @return string
	 */
	public function getExtensionName() {
		return $this->extensionName;
	}

	/**
	 * @param string $pluginName
	 * @return void
	 */
	public function setPluginName($pluginName) {
		$this->pluginName = $pluginName;
	}

	/**
	 * @return string
	 */
	public function getPluginName() {
		return $this->pluginName;
	}

	/**
	 * @param integer $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return integer
	 */
	public function getStatus() {
		return $this->status;
	}

}
