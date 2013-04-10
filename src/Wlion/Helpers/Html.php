<?php

namespace Wlion\Helpers;

use Illuminate\Routing\UrlGenerator as Url;

class Html {
	/**
	 * Default encoding.
	 * 
	 * @var string
	 */
	protected $encoding = 'UTF-8';
	
	/**
	 * Url generator instance
	 * 
	 * @var Illuminate\Routing\UrlGenerator
	 */
	protected $url;
	
	/**
	 * Build new instance.
	 * 
	 * @param UrlGenerator $url
	 */
	public function __construct(Url $url) {
		$this->url = $url;
	}
	
	/**
	 * Return domain based on current url.
	 * 
	 * @return string
	 */
	public function getDomain() {
		$parse = parse_url($this->url->getRequest()->root());
		return $parse['host'];
	}
	
	/**
	 * Generate an HTML link.
	 * 
	 * @param  string $url
	 * @param  string $title
	 * @param  array  $attributes
	 * @param  array  $parameters
	 * @param  bool   $https
	 * @return string
	 */
	public function to($url, $title = NULL, $attributes = array(), $parameters = array(), $https = FALSE) {
		// Build url
		$url = $this->url->to($url, $parameters, $https);
		
		// Title?
		if (is_null($title)) {
			$title = $url;
		}
		
		// Return
		return '<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $this->entities($title) . '</a>';
	}
	
	/**
	 * Generate an HTML link to a named route.
	 * 
	 * @param  string $name
	 * @param  string $title
	 * @param  array  $attributes
	 * @param  array  $parameters
	 * @param  bool   $https
	 * @return string
	 */
	public function route($name, $title = NULL, $attributes = array(), $parameters = array(), $https = FALSE) {
		return $this->to($this->url->route($name), $title, $attributes, $parameters, $https);
	}
	
	/**
	 * Build mailto anchor tag, obfuscating email address.
	 * 
	 * @param  string $email
	 * @param  string $title
	 * @param  array  $attributes
	 * @return string
	 */
	public function mailto($email, $title = NULL, $attributes = NULL) {
		// Check input
		if (empty($email)) {
			return $title;
		}
		
		// Remove the subject or other parameters that do not need to be encoded
		if (strpos($email, '?') !== FALSE) {
			// Extract the parameters from the email address
			list($email, $params) = explode('?', $email, 2);
			
			// Make the params into a query string, replacing spaces
			$params = '?' . str_replace(' ', '%20', $params);
		} else {
			// No parameters
			$params = '';
		}
		
		// Obfuscate email address
		$email = $this->_obfuscate($email);
		
		// Title defaults to the encoded email address
		if (empty($title)) {
			$title = $email;
		}
		
		// Encoded start of the href="" is a static encoded version of 'mailto:'
		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;' . $email . $params . '"' . $this->attributes($attributes) . '>' . $title . '</a>';
	}
	
	/**
	 * Obfuscate email address.
	 * 
	 * @param  string $email
	 * @return string
	 */
	public function email($email) {
		return str_replace('@', '&#64;', $this->_obfuscate($email));
	}
	
	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * 
	 * @param  array $attributes
	 * @return string
	 */
	public function attributes($attributes) {
		// Check input
		if (empty($attributes)) {
			return '';
		} elseif (is_string($attributes)) {
			return ' ' . $attributes;
		}
		
		// Build attribute string
		$string   = '';
		$compiled = array();
		foreach ((array)$attributes as $k => $v) {
			if (!is_null($v)) {
				if (is_numeric($k)) {
					$k = $v;
				}
				$string .= ' ' . $k . '="' . $this->entities($v) . '"';
			}
		}
		
		// Return
		return $string;
	}
	
	/**
	 * Convert HTML special characters.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function specialchars($string) {
		return htmlspecialchars($string, ENT_QUOTES, $this->encoding, FALSE);
	}
	
	/**
	 * Convert HTML characters to HTML entities.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function entities($string) {
		return htmlentities($string, ENT_QUOTES, $this->encoding, FALSE);
	}
	
	/**
	 * Obfuscate string.
	 * 
	 * @param  string $string
	 * @return string
	 */
	protected function _obfuscate($string) {
		$safe = '';
		foreach (str_split($string) as $letter) {
			switch (($letter === '@') ? rand(1, 2) : rand(1, 3)) {
				case 1:
					$safe .= '&#' . ord($letter) . ';';
				break;
				case 2:
					$safe .= '&#x' . dechex(ord($letter)) . ';';
				break;
				case 3:
					$safe .= $letter;
			}
		}
		return $safe;
	}
}
