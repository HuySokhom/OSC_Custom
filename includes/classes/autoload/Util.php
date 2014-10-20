<?php

class Util {
	
	
	private function __construct() {
		// singleton
	}
	
	/**
	 * encodes a string / array values to utf8 using Encode::toUTF8
	 * @param (mixed) $mixed
	 */
	static function utf8Encode( $mixed ) {
		if( is_array( $mixed ) ) {
			foreach( $mixed as $key => $value ) {
				$mixed[$key] = self::utf8Encode( $value );
			}
		} else {
			$mixed = Encoding::toUTF8( $mixed );	
		}
		
		return $mixed;
	}

	static function closetags($content) {
		preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $content, $result);
		$openedtags = $result[1];
		preg_match_all('#</([a-z]+)>#iU', $content, $result);
		$closedtags = $result[1];
		$len_opened = count($openedtags);
		if (count($closedtags) == $len_opened) {
			return $content;
		}
		$openedtags = array_reverse($openedtags);
		for ($i=0; $i < $len_opened; $i++) {
			if (!in_array($openedtags[$i], $closedtags)) {
				$content .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i], $closedtags)]);
			}
		}
		return $content;
	}
	
	static function linkifyString( $string ) {
		$string = preg_replace(
			array(
				'/[\pP]/',
				'/[\W]+/'
			), 
			array(
				'',
				'-'
			),
			$string 
		);
		
		return trim($string, ' -');
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
	 * returns a human-sensical "time ago" string based on $date
	 * - based on timeAgo Smarty modifier 
	 * @author   Stephan Otto
	 * 
	 * @param (mixed) $date
	 */
	static function dateTimeAgo( $date, $debug = false ) {
		// english
		$timeStrings = array(   
			'just now',            // 0       <- now or future posts :-)
			'second ago', 'seconds ago',    // 1,1
			'minute ago', 'minutes ago',      // 3,3
			'hour ago', 'hours ago',   // 5,5
			'day', 'days',         // 7,7
			'week', 'weeks',      // 9,9
			'month', 'months',      // 11,12
			'year', 'years'      // 13,14
		);
		
		$dateTimestamp = ( strtotime($date) ? strtotime($date) : $date );
		
		if( !is_int($date) ) {
			$sec = time() - $dateTimestamp;
		} else {
			$sec = time() - $date;
		}
		
// 		if( $debug == true ) {
// 			return 'date: ' . $sec;
// 		}
		
		if ( $sec <= 0) return $timeStrings[0];
		
		if ( $sec < 2) return $sec." ".$timeStrings[1];
		if ( $sec < 60) return $sec." ".$timeStrings[2];
		
		$min = $sec / 60;
		if ( floor($min+0.5) < 2) return floor($min+0.5)." ".$timeStrings[3];
		if ( $min < 60) return floor($min+0.5)." ".$timeStrings[4];
		
		$hrs = $min / 60;
		echo ($debug == true) ? "hours: ".floor($hrs+0.5)."<br />" : '';
// 		if ( floor($hrs+0.5) < 2) return floor($hrs+0.5)." ".$timeStrings[5];
// 		if ( $hrs < 24) return floor($hrs+0.5)." ".$timeStrings[6];
		
		
		$midnightTs = strtotime(date('Y-m-d', $dateTimestamp) . ' 00:00:00');
		// today
		if( $midnightTs == strtotime(date('Y-m-d') . ' 00:00:00') ){
			return (
				floor($hrs+0.5) . " " . (
					floor($hrs+0.5) < 2
						?
					$timeStrings[5]
						:
					$timeStrings[6]
				) 
			);
		}
		
		// yesterday
		elseif( $midnightTs > strtotime(date('Y-m-d') . ' 00:00:00') - 86459 ){
			return 'Yesterday';
		}
		
		// anything earlier..
		else {
			return date('m/d/Y', $dateTimestamp);
		}
		
		// nothing goes beyond this point..
		
		
// 		$days = $hrs / 24;
// 		echo ($debug == true) ? "days: ".floor($days+0.5)."<br />" : '';
		
// 		if( floor($days+0.5) < 2 ) {
// 			return 'yesterday';
// 		} else {
// 			return date('m/d/Y', $dateTimestamp);
// 		}
			
// 		if ( floor($days+0.5) < 2) return floor($days+0.5)." ".$timeStrings[7];
// 		if ( $days < 7) return floor($days+0.5)." ".$timeStrings[8];
		
// 		$weeks = $days / 7;
// 		echo ($debug == true) ? "weeks: ".floor($weeks+0.5)."<br />" : '';
// 		if ( floor($weeks+0.5) < 2) return floor($weeks+0.5)." ".$timeStrings[9];
// 		if ( $weeks < 4) return floor($weeks+0.5)." ".$timeStrings[10];
		
// 		$months = $weeks / 4;
// 		if ( floor($months+0.5) < 2) return floor($months+0.5)." ".$timeStrings[11];
// 		if ( $months < 12) return floor($months+0.5)." ".$timeStrings[12];
		
// 		$years = $weeks / 51;
// 		if ( floor($years+0.5) < 2) return floor($years+0.5)." ".$timeStrings[13];
		
		
// 		return floor($years+0.5)." ".$timeStrings[14];		
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
	 * determines if ironically named "smart" quotes are present in a string
	 * 
	 * @param (string) $string
	 * @return (bool) true if present, false otherwise
	 */
	static function isSmartQuotesPresent($string) {
		$search = array(
			chr(145),
			chr(146),
			chr(147),
			chr(148),
			chr(151)
		);
		
		foreach( $search as $char ) {
			if( strpos($string, $char) !== false ) {
				return true;
			}
		}
		
		return false;
	}
 
	
	/**
	 * 
	 * logs an event for later evaluation
	 * 
	 * Expected Params:
	 * - (string) logfile | absolute path to logfile
	 * - (string) event_name
	 * - (array) variables
	 */
	static function logEvent( $params = array() ){
		ob_start();
		echo "\n\n ============================================= \n [***LOG_BEGIN***] \n";
		echo "event: " . $params['event_name'] . "\n";
		echo "time: " . date(DATE_RFC2822) . "\n\n";
		
		// variables
		if( is_array($params['variables']) ){
			echo "Variables: \n";
			foreach( $params['variables'] as $key => $val ){
				echo "\t- [$key]: \n";
				var_dump( $val );
				echo "\n\n";
			}
		}
		
		// server
		echo "SERVER: \n";
		var_dump( $_SERVER );
		echo "\n\n";
		
		// get
		echo "GET: \n";
		var_dump( $_GET );
		echo "\n\n";
		
		// post
		echo "POST: \n";
		var_dump( $_POST );
		echo "\n\n";
		
		// session
		echo "SESSION: \n";
		var_dump( $_SESSION );
		echo "\n\n";
		
		// cookie
		echo "COOKIE: \n";
		var_dump( $_COOKIE );

		
		echo "\n\n [***LOG_END***] \n ============================================= \n\n";
		
		
		$output = ob_get_contents();
		file_put_contents(
			$params['logfile'],
			$output,
			FILE_APPEND
		);
		
		ob_end_clean();
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
	
}


