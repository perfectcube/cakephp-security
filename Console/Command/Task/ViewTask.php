<?php

App::uses('ViewTokenizer', 'Security.Lib');
App::uses('SecurityBaseTask', 'Security.Console/Command/Task');

/**
 * Controller checker
 *
 * Uses the ViewTokenizer and tokenizes
 * all files ending in .ctp
 *
 */
class ViewTask extends SecurityBaseTask {

/**
 * Pattern to match files with
 *
 * @var string
 */
 	protected $_filePattern = '#^.+.ctp$#i';

/**
 * Tokenizer class to use
 *
 * @var string
 */
	protected $_tokenizerClass = 'ViewTokenizer';

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
					$this->_outputError('die() statements not allowed in views', $error);
					break;

				case T_ECHO:
					$this->_outputError('echo() statements not allowed in views', $error);
					break;

				case T_VARIABLE:
					$this->_outputError('Unsafe variable output for "' . $error[1] . '". Please wrap in h()', $error);
					break;

				case T_REQUIRE:
					$this->_outputError('Using require() inside a View is not allowed, use $this->element()', $error);
					break;

				case T_INCLUDE:
					$this->_outputError('Using include() inside a View is not allowed, use $this->element()', $error);
					break;

				default:
					debug(token_name($error[0]));
					debug($error);
					break;
			}
		}
	}

}
