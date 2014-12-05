<?php

class RestApiResources {
	
	private function __construct() {
		// singleton
	}
	
	static private 
		$dbRes
	;
	
	static public function getDbRes() { 
		return self::$dbRes;
	}
	
	static public function setDbRes( $dbRes ) {
// 		if( !is_resource( $dbRes ) ) {
// 			throw new Exception("not a resource");
// 		}
		
		self::$dbRes = $dbRes;
	}
		
}