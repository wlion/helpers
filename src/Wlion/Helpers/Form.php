<?php

namespace Wlion\Helpers;

use Wlion\Helpers\Html;

class Form {
	/**
	 * Html helper instance.
	 * 
	 * @var Wlion\Helpers\Html
	 */
	protected $html;
	
	/**
	 * Build new instance.
	 * 
	 * @param Html $html
	 */
	public function __construct(Html $html) {
		$this->html = $html;
	}
	
	/**
	 * Builds select list options. Assumes open and close tags exist.
	 * 
	 * @param array  $options  list options
	 * @param string $selected selected option(s)
	 * @param string $var      loop variable used as option value
	 * @return string
	 */
	public function selectList($options, $selected = NULL, $var = 'k') {
		// Init return
		$list = '';
		
		// Set selected options
		if (!is_array($selected)) {
			$selected = array($selected);
		}
		
		// List options, flag selected value
		foreach ((array)$options as $k => $v) {
			$value = $this->html->specialchars(${$var});
			$text  = $this->html->specialchars($v);
			
			$list .= '<option value="' . $value . '"' . ((in_array($value, $selected)) ? ' selected="selected"' : '') . '>' . $text . '</option>' . PHP_EOL;
		}
		
		// Return
		return $list;
	}
}
