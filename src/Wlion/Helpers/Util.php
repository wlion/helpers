<?php

namespace Wlion\Helpers;

use Log;
use Image;

class Util {
	/**
	 * Crop image.
	 * 
	 * @param  string $image
	 * @param  string $copy
	 * @param  array  $specs
	 * @return bool
	 */
	public function cropImage($image, $copy, $specs = array()) {
		// Begin processing
		if ($img = new Image($image)) {
			// Crop and save
			$img->crop($specs['width'], $specs['height'], $specs['left'], $specs['top']);
			$img->save($copy);
			
			// Return file status
			return file_exists($copy);
		}
		
		// Default return
		return FALSE;
	}
	
	/**
	 * Copy image and resize as needed.
	 * 
	 * @param  string $image
	 * @param  string $copy
	 * @param  array  $dimensions
	 * @return bool
	 */
	public function copyImage($image, $copy, $dimensions = array()) {
		// Begin processing
		if ($img = new Image($image)) {
			// Set values
			$width      = $img->width;
			$height     = $img->height;
			$keep_ratio = FALSE;
			if (isset($dimensions['exact_w']) and isset($dimensions['exact_h'])) {
				// Exact size
				if (($width != $dimensions['exact_w']) or ($height != $dimensions['exact_h'])) {
					$width  = $dimensions['exact_w'];
					$height = $dimensions['exact_h'];
				}
			} elseif (isset($dimensions['exact_w'])) {
				// Exact width
				if ($width != $dimensions['exact_w']) {
					$width      = $dimensions['exact_w'];
					$height     = NULL;
					$keep_ratio = TRUE;
				}
			} elseif (isset($dimensions['exact_h'])) {
				// Exact height
				if ($height != $dimensions['exact_h']) {
					$width      = NULL;
					$height     = $dimensions['exact_h'];
					$keep_ratio = TRUE;
				}
			} elseif (isset($dimensions['max_w']) and isset($dimensions['max_h'])) {
				// Smaller ratio will satisfy both max_w and max_h
				if (($width > $dimensions['max_w']) or ($height > $dimensions['max_h'])) {
					$ratio  = min(($dimensions['max_w'] / $width), ($dimensions['max_h'] / $height));
					$width  = $ratio * $width;
					$height = $ratio * $height;
				}
			} elseif (isset($dimensions['max_w'])) {
				// Max width
				if ($width > $dimensions['max_w']) {
					$width      = $dimensions['max_w'];
					$height     = NULL;
					$keep_ratio = TRUE;
				}
			} elseif (isset($dimensions['max_h'])) {
				// Max height
				if ($height > $dimensions['max_h']) {
					$width      = NULL;
					$height     = $dimensions['max_h'];
					$keep_ratio = TRUE;
				}
			}
			
			// Resize
			if ($width != $img->width or $height != $img->height) {
				$img->resize($width, $height, $keep_ratio);
			}
			
			// Save copy
			$img->save($copy);
			
			// Return file status
			return file_exists($copy);
		}
		
		// Default return
		return FALSE;
	}
	
	/**
	 * Clean all input before use.
	 * 
	 * @param  mixed $input
	 * @return mixed
	 */
	public function sanitizeInput($input) {
		// Load HTMLPurifier and set config
		\HTMLPurifier_Bootstrap::registerAutoload();
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('HTML.TidyLevel', 'none');
		$config->set('Cache.SerializerPath', app_path() . '/storage/cache/htmlpurifier');
		$config->set('Cache.SerializerPermissions', 0777);
		$config->set('HTML.SafeObject', TRUE);
		$config->set('Attr.EnableID', TRUE);
		$purifier = new \HTMLPurifier($config);
		
		// Clean input
		if (is_array($input)) {
			$new_array = array();
			foreach ($input as $k => $v) {
				$new_array[Util::sanitizeInput($k)] = Util::sanitizeInput($v);
			}
			return $new_array;
		}
		
		// Trim and decode
		$input = html_entity_decode(trim($input), ENT_QUOTES);
		
		// No need to clean empty strings or numeric data
		if ($input === '' or is_numeric($input)) {
			return $input;
		}
		
		// Return cleaned input
		return $purifier->purify($input);
	}
	
	
	/**
	 * Limit the number of characters in a string.
	 * 
	 * @param  string  $value
	 * @param  integer $limit
	 * @param  string  $end
	 * @return string
	 */
	public function limitChars($value, $limit = 100, $end = '...') {
		if (mb_strlen($value, 'UTF-8') <= $limit) {
			return $value;
		}
		return mb_substr($value, 0, $limit, 'UTF-8') . $end;
	}
	
	/**
	 * Return list of options for an enum field.
	 * 
	 * @param  string $table
	 * @param  string $field
	 * @return array
	 */
	public function getEnumOptions($table, $field) {
		// Init return
		$list = array();
		
		// Get options
		$res = \DB::select('SHOW COLUMNS FROM `' . $table . '`');
		foreach ($res as $k => $v) {
			if ($v->Field == $field) {
				$options = explode("','", substr($v->Type, 6, -2));
				foreach ((array)$options as $option) {
					$list[$option] = $option;
				}
				break;
			}
		}
		
		// Return
		return $list;
	}
	
	/**
	 * Output value and kill script.
	 * 
	 * @param  mixed $value
	 * @return void
	 */
	public function dd($value) {
		exit('<pre>' . var_dump($value) . '</pre>');
	}
}
