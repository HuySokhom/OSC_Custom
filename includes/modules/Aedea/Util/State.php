<?php

namespace Aedea\Util;

use Aedea\Core\Singleton;

class State extends Singleton {
	
	static public 
		$_properties = array()
	;
	
	static public function init(){
		self::setRequestInfo();
	}
		
	static protected function setRequestInfo(){
		$info = array();
		
		// determine request type
		$info['type'] = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
		
		// determime relative path
		if( $info['type'] == 'SSL' ){
			$info['webroot'] = DIR_WS_HTTPS_CATALOG;
		} else {
			$info['webroot'] = DIR_WS_HTTP_CATALOG;
		}
		
		// determine request relative to webroot, page, params
		$info['request'] = substr(
			$_SERVER['REQUEST_URI'], 
			strlen($info['webroot'])
		);
		
		$request_pieces = explode('?', $info['request']);
		$info['page'] = $request_pieces[0];
		$info['parameters'] = $request_pieces[1];
		
		self::$_properties['request'] = $info;
	}
	
	
	
}
