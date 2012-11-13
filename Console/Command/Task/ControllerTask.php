<?php

App::uses('SecurityBaseTask', 'Security.Console/Command/Task');
App::uses('ControllerTokenizer', 'Security.Lib');

/**
 * Controller checker
 *
 * Uses the ControllerTokenizer and tokenizes
 * all files ending in Controller.php
 *
 */
class ControllerTask extends SecurityBaseTask {

/**
 * Pattern to match files with
 *
 * @var string
 */
	protected $_filePattern = '#^.+Controller.php$#i';

/**
 * Tokenizer class to use
 *
 * @var string
 */
	protected $_tokenizerClass = 'ControllerTokenizer';

/**
 * Output user friendly error messages
 *
 * The message is based on the token ID
 *
 * @param array $errors List of errors for the file
 * @param string $file Path to the file that was tokenized
 * @return void
 */
	protected function _outputFriendlyErrors($errors, $file) {
		$file = $this->_normalizeFilePath($file);
		$this->log(sprintf('%s (%d errors)', $file, sizeof($errors)), 'info');

		foreach ($errors as $error) {
			switch ($error[0]) {
				case T_EXIT:
					$this->_outputError('die() statements not allowed in controllers', $error);
					break;

				case T_ECHO:
					$this->_outputError('echo() statements not allowed in controllers', $error);
					break;

				default:
					debug($error);
					break;
			}
		}
	}

}
