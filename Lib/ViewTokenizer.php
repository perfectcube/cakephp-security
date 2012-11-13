<?php
// http://www.php.net/manual/en/tokens.php
// http://www.php.net/manual/en/function.token-get-all.php
//

App::uses('BaseTokenizer', 'Security.Lib');

class ViewTokenizer extends BaseTokenizer {

/**
 * Check file for security errors
 *
 * @return boolean
 */
	public function check() {
		$this->_errors = array();
		$this->_resetState();

		foreach ($this->_tokens as $token) {
			if (!is_array($token)) {
				// Concatenation should mark output as not safe
				if (in_array($token, array('.'))) {
					$this->_safe = false;
				}

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
					$this->_errors[] = $token;
					break;

				// don't care for whitespace
				case T_WHITESPACE:
					break;

				case T_OPEN_TAG_WITH_ECHO: // <?= should be treated as normal T_ECHO
				case T_PRINT: // 'print' should be treated as echo
				case T_ECHO: // Normal <?php echo
					$this->_setState(T_ECHO);
					break;

				// Close tag found, reset our state
				case T_CLOSE_TAG:
					$this->_resetState();
					break;

				// Strings and variables is what we want to check for
				case T_STRING:
				case T_VARIABLE:
					// If we currently aren't echoing, we don't care
					if ($this->_getState() !== T_ECHO) {
						break;
					}

					// If we are echoing with "$this" we assume it's a helper
					// and that helpers know how to escape on their own
					if ($token[1] === '$this') {
						$this->_safe = true;
						break;
					}

					// If our echo includes a call to h() it's safe
					if ($token[0] == T_STRING && $token[1] === 'h') {
						$this->_safe = true;
						break;
					}

					// If the variable is echo'd directly, we fail at escaping
					if ($token[0] === T_VARIABLE && !$this->_safe) {
						$this->_errors[] = $token;
						break;
					}

					break;

				default:
					break;
			}
		}

		return empty($this->_errors);
	}
}
