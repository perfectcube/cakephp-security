<?php
class CheckShell extends AppShell {

	public $tasks = array(
		'Security.Controller',
		'Security.View',
	);

/**
 * Gets the option parser instance and configures it.
 * By overriding this method you can configure the ConsoleOptionParser before returning it.
 *
 * @return ConsoleOptionParser
 * @link http://book.cakephp.org/2.0/en/console-and-shells.html#Shell::getOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		return $parser
			->description('Hello World')
			->addOption('path', array(
				'help' => 'Check source files for different violations of Nodes code standards'
			))
			->addSubcommand('controller', array(
				'help' => 'Check Controllers inside the app',
			))
			->addSubcommand('view', array(
				'help' => 'Check Views inside the app',
			));
	}

}
