<?php

abstract class BaseTokenizer {

/**
 * List of tokens from the PHP file
 *
 * @var array
 */
	protected $_tokens;

/**
 * List of errors found in the file
 *
 * @var array
 */
	protected $_errors = array();

/**
 * Variable to check if the current token stream should be considered safe
 * or not
 *
 * @var boolean
 */
	protected $_safe = false;

/**
 * Current internal state of the line
 *
 * @var array
 */
	protected $_state;

/**
 * Constructor
 *
 * @param mixed $string
 * @return void
 */
	public function __construct($options = array()) {
		if (!empty($options['string'])) {
			$this->_tokenize($options['string']);
		}

		if (!empty($options['file'])) {
			$this->_tokenize(file_get_contents($options['file']));
		}
	}

	abstract public function check();

/**
 * Set the string to be tokenized
 *
 * @param string $string
 * @return void
 */
	public function setString($string) {
		$this->_tokenize($string);
	}

	public function loadFile($file) {
		$this->_tokenize(file_get_contents($file));
	}

/**
 * Get list of errors
 *
 * @return array
 */
	public function getErrors() {
		return $this->_errors;
	}

/**
 * Tokenize a string
 *
 * @param string
 * @return void
 */
	protected function _tokenize($string) {
		$this->_tokens = token_get_all($string);
	}

/**
 * Reset the internal state
 *
 * @return void
 */
	protected function _resetState() {
		$this->_state = null;
		$this->_safe = false;
	}

/**
 * Set the internal state
 *
 * @return void
 */
	protected function _setState($state) {
		$this->_state = $state;
	}

/**
 * Get the internal state
 *
 * @return void
 */
	protected function _getState() {
		return $this->_state;
	}

}
