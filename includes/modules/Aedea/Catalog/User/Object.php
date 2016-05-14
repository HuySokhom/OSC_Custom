<?php

namespace Aedea\Catalog\User;

use 
	Aedea\Core\Database\StdObject
;

abstract class Object extends StdObject {
	
	protected 
		$nameFirst
		, $nameLast
		, $email
	;

	public function __construct( $params = array() ){
		parent::__construct($params);
	}
	
	public function setNameFirst( $string ){
		$this->nameFirst = (string)$string;
	}
	
	public function getNameFirst(){
		return $this->nameFirst;
	}
	
	public function setNameLast( $string ){
		$this->nameLast = (string)$string;
	}
	
	public function getNameLast(){
		return $this->nameLast;
	}
	
	public function setEmail( $string ){
		// @todo: add validation
		$this->email = (string)$string;
	}
	
	public function getEmail(){
		return $this->email;
	}
}
