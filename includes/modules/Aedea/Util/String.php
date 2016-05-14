<?php

namespace Aedea\Util;

use 
	Aedea\Core\Singleton
;

class String extends Singleton {
	
	static public function startsWith($haystack, $needle){
		return substr($haystack, 0, strlen($needle)) === $needle;
	}
 
	static public function endsWith($haystack, $needle){
		if( ! ($length = strlen($needle)) ){
			return true;
		}
	
		return substr($haystack, -$length) === $needle;
	}
	
}
