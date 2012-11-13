<?php

// http://www.php.net/manual/en/tokens.php
// http://www.php.net/manual/en/function.token-get-all.php
//

App::uses('BaseTokenizer', 'Security.Lib');

class ControllerTokenizer extends BaseTokenizer {

	public function check() {
		$this->_errors = array();
		$this->_resetState();

		foreach ($this->_tokens as $token) {
			if (!is_array($token)) {
				continue;
			}

			// $name = token_name($token[0]);
			// debug(compact('token', 'name'));

			switch ($token[0]) {
				case T_EXIT: // exit() not allowed
				case T_EVAL: // eval() not allowed
				case T_REQUIRE: // require() not allowed
				case T_REQUIRE_ONCE: // require_once not allowed
				case T_INCLUDE: // include() not allowed
				case T_INCLUDE_ONCE: // include_once() not allowed

				case T_PRINT: // 'print' should be treated as echo
				case T_ECHO: // Normal <?php echo
					$this->_errors[] = $token;
					break;

				default:
					break;
			}
		}

		return empty($this->_errors);
	}

}
