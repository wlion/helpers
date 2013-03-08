<?php

namespace Wlion\Helpers;

class Format {
	/**
	 * Returns clean filename.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function cleanFilename($string) {
		$search = array(
			'\xe2\x80\x98', // left single quote
			'\xe2\x80\x99', // right single quote
			'\xe2\x80\x9c', // left double quote
			'\xe2\x80\x9d', // right double quote
			'(',            // parenthesis
			')'
		); 
		$replace = array(
			'_',
			'_',
			'_',
			'_',
			'_',
			'_'
		);
		return str_replace($search, $replace, $string);
	}
	
	/**
	 * Format a MySQL date.
	 * 
	 * @param  string $date
	 * @return string
	 */
	public function mysqlDate($date) {
		return date('Y-m-d H:i:s', strtotime($date));
	}
	
	/**
	 * Format a MySQL time.
	 * 
	 * @param  string $time
	 * @return string
	 */
	public function mysqlTime($time) {
		return date('H:i:s', strtotime($time));
	}
}
