<?php

class Util {

	private function __construct() {
		// singleton
	}
	
	static function startsWith($haystack,$needle,$case=true) {
		if($case)
			return strpos($haystack, $needle, 0) === 0;
	
		return stripos($haystack, $needle, 0) === 0;
	}
	
	static function endsWith($haystack,$needle,$case=true) {
		$expectedPosition = strlen($haystack) - strlen($needle);
	
		if($case)
			return strrpos($haystack, $needle, 0) === $expectedPosition;
	
		return strripos($haystack, $needle, 0) === $expectedPosition;
	}
	
	/**
	 * encodes a string / array values to utf8
	 * @param (mixed) $mixed
	 */
	static function utf8Encode( $mixed ) {
		if( is_array( $mixed ) ) {
			foreach( $mixed as $key => $value ) {
				$mixed[$key] = self::utf8Encode( $value );
			}
		} else {
			if( !mb_check_encoding( $mixed, 'UTF-8') ) {
				$mixed = utf8_encode( $mixed );
			}
		}
	
		return $mixed;
	}
	
	/**
	 * truncate a string to nearest whole word
	 * @param (string) $string
	 * @param (int) $your_desired_width
	 * @param (string) $ending
	 * @return (string) truncated string
	 */
	static function truncateString($string, $length_limit, $ending = false) {
		$parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
		$parts_count = count($parts);
	
		$length = 0;
		$last_part = 0;
		for (; $last_part < $parts_count; ++$last_part) {
			$length += strlen($parts[$last_part]);
			if ($length > $length_limit) {
				break;
			}
		}
	
		$truncated_string = implode(array_slice($parts, 0, $last_part));
		if( strlen($ending) > 0 && strlen( $truncated_string ) < strlen( $string )) {
			$truncated_string .= $ending;
		}
	
		return $truncated_string;
	}
	
	
	/**
	 * truncateHtml can truncate a string up to a number of characters while preserving whole words and HTML tags
	 * @author probably CakePHP
	 *
	 * @param string $text String to truncate.
	 * @param integer $length Length of returned string, including ellipsis.
	 * @param string $ending Ending to be appended to the trimmed string.
	 * @param boolean $exact If false, $text will not be cut mid-word
	 * @param boolean $considerHtml If true, HTML tags would be handled correctly
	 *
	 * @return string Trimmed string.
	 */
	static function truncateHtml($text, $length, $ending = '', $exact = false, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';
			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
						// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
						// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}
				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}
				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}
		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}
		// add the defined ending to the text
		$truncate .= $ending;
		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}
	
	/**
	 * determines whether or not a url is relative
	 * 
	 * Expected Parameters:
	 * - (string) url
	 */
	static function isRelativeUrl( $params ) {
		foreach(array(
			'http://',
			'https://',
			'//'
		) as $str ){
			if( strpos($params['url'], $str) === 0 ) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * uses assetic to consolidate and compress files
	 * 
	 * Expected Parameters: 
	 * - (string) filename | name of asset
	 * - (string) type | supported: js, css
	 * - (mixed) files | file(s) to minify
	 */
	static function Assetify( $params ){
		$files = is_array($params['files']) ? $params['files'] : array($params['files']);
		
		// if minification is not enabled, output the files normally
		if( !SETTING_ENABLE_MINIFICATION ) {
			$output = '';
			switch( $params['type'] ) {
				case 'js':
					foreach( $files as $file ) {
						$output .= '<script type="text/javascript" src="' . $file . '"></script>';
					}
					break;
						
				case 'css':
					$output .= '<style type="text/css">' . "\n";
					foreach( $files as $file ) {
						$output .= '@import url("' . $file . '");' . "\n";
					}
					$output .= '</style>';
					break;
			}
		
			return $output;
		}
		
		
		// Assetic operations..
		$factory = new Assetic\Factory\AssetFactory( DIR_FS_CATALOG );
		$factory->addWorker(new Assetic\Factory\Worker\CacheBustingWorker(Assetic\Factory\Worker\CacheBustingWorker::STRATEGY_MODIFICATION));
		
		// filter manager
		$fm = new Assetic\FilterManager();
		
		switch( $params['type'] ) {
			case 'js':
				$factory->setDefaultOutput('assets/*');
				$fm->set('min', new Assetic\Filter\JSMinFilter());
				break;
		
			case 'css':
				$factory->setDefaultOutput('assets/*');
				$fm->set('min', new Assetic\Filter\CssMinFilter());
				break;
		}
		$factory->setFilterManager($fm);
		
		$asset = $factory->createAsset(
			$files,
			array(
				'min'
			),
			array(
				'name' => $params['filename']
			)
		);
		
		// only write the cache file if it does not already exist..
		if( !file_exists( DIR_FS_CATALOG . $asset->getTargetPath()) ) {
			$writer = new Assetic\AssetWriter(DIR_FS_CATALOG);
			$writer->writeAsset($asset);
		
			// TODO: write some code to garbage collect files of a certain age?
			// possible alternative, modify CacheBustingWorker to have option to append a timestamp instead of a hash
		}
		
		$output = '';
		switch( $params['type'] ) {
			case 'js':
				$output .= '<script type="text/javascript" src="' . $asset->getTargetPath() . '"></script>';
				break;
					
			case 'css':
				$output .= '<link rel="stylesheet" type="text/css" href="' . $asset->getTargetPath() . '" />';
		}
		
		return $output;
	}
	
	/**
	 * converts ironically named "smart" quotes to "real" quotes
	 * - also converts other common microsoft characters to their standard counterparts
	 * Author: http://shiflett.org/blog/2005/oct/convert-smart-quotes-with-php
	 * @param (string) $string
	 * @return (string) | converted string
	 */
	static function convertSmartQuotes($string) {
		$search = array(
			'‘',
			'’',
			'“',
			'”',
			'…',
			'–',
			'—'
		);
	
		$replace = array(
			"'",
			"'",
			'"',
			'"',
			'...',
			'-',
			'-'
		);
	
		return str_replace($search, $replace, $string);
	}

	/**
	 * converts camelCase to snake_case
	 * @param (string) $val
	 * @return (string)
	 */
	static function camelToSnake($val) {
		return preg_replace_callback(
			'/[A-Z]/',
			create_function('$match', 'return "_" . strtolower($match[0]);'),
			$val
		);
	}
	
	/**
	 * converts snake_case to camelCase
	 * @param (string) $val
	 * @return (string)
	 */
	static function snakeToCamel($val) {
		$val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
		$val = strtolower(substr($val,0,1)).substr($val,1);
		return $val;
	}
	
	
	static function randString(
		$length, 
		$charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
	){
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count-1)];
		}
		return $str;
	}
	
}












