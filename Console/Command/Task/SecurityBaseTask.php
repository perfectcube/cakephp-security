<?php

App::uses('AppShell', 'Console/Command');

/**
 * Base task for different tokenizer tasks
 *
 */
abstract class SecurityBaseTask extends AppShell {

/**
 * All tasks take one argument, the path to scan
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->addArgument('path', array('help' => 'Path to scan', 'required' => true));
	}

/**
 * Main method
 *
 */
	public function main() {
		if (empty($this->args)) {
			return $this->_displayHelp();
		}

		$dir = realpath($this->args[0]);

		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::CHILD_FIRST);
		$iterator = new RegexIterator($iterator, $this->_filePattern, RecursiveRegexIterator::GET_MATCH);

		$tokenizer = new $this->_tokenizerClass();
		foreach ($iterator as $file) {
			$file = current($file);

			$this->file = $file;
			$tokenizer->loadFile($file);

			if (!$tokenizer->check()) {
				$this->_outputFriendlyErrors($tokenizer->getErrors(), $file);
			}
		}
	}

/**
 * Output error message
 *
 * The message is a bit indented, and include line number from the error message
 *
 * @param string $message The original error message
 * @param array $error The error array from the tokenizer
 * @return void
 */
	protected function _outputError($message, $error) {
		$this->log(sprintf('  Line %3d - %s', $error[2], $message), 'warning');
	}

/**
 * Tries to normalize paths for output based on the app/ folder within the realpath()
 *
 * @param string $path
 * @return string
 */
	protected function _normalizeFilePath($path) {
		if (preg_match('#(app/.+)#', $path, $match)) {
			return $match[0];
		}

		return $path;
	}

}
