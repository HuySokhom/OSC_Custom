<?php

namespace Aedea\Core\Database;

use Aedea\Core\Database\StdObject;

abstract class StdObjectPolymorphic extends StdObject {
	protected 
		$properties
	;
	
	abstract public function getObjectType();
	
	public function __get($k){
		if( isset($this->properties[$k]) ){
			return $this->properties[$k];
		}
		
		throw new \Exception("Unknown Property [$k]");
	}
	
	public function __set($k, $v){
		$this->properties[$k] = $v;
	}
}
