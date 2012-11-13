<?php

if (!function_exists('raw')) {

/**
 * Method to mark that you intend to output the raw value of your variable
 *
 * If your variables are wrapped within this method, it won't raise an error
 *
 * @param string
 * @return string
 */
	function raw($string) {
		return $string;
	}
}
