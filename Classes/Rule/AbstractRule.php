<?php
class Tx_ExtbaseRealurl_Rule_AbstractRule {

	/**
	 * @var string
	 */
	protected $commandPattern = '/([a-z0-9]+)\(\'?([a-z0-9_]+)\'?\)\s?/i';

	/**
	 * @var string
	 */
	protected $variablePattern = '/\s?\$([a-z0-9]+)/i';

	/**
	 * @var string
	 */
	protected $argumentName;

	/**
	 * @var array
	 */
	protected $commandToPropertyMap = array();

	/**
	 * @param string $annotation
	 * @return void
	 */
	public function detectArgumentName($annotation) {
		$argumentName = NULL;
		$matches = array();
		$matched = preg_match_all($this->variablePattern, $annotation, $matches);
		if ($matched) {
			$this->argumentName = array_pop(array_pop($matches));
		}
	}

	/**
	 * @return array
	 */
	public function getArrayCopy() {
		$properties = array();
		foreach ($this->commandToPropertyMap as $propertyName) {
			$properties[$propertyName] = $this->$propertyName;
		}
		return $properties;
	}

}
