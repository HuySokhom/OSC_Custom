<?php

namespace Aedea\Catalog\Address;

use 
	Aedea\Core\Database\StdObject
;

class Object extends StdObject {
	
	protected 
		$company
		, $nameFirst
		, $nameLast
		, $streetAddress
		, $postalCode
		, $city
		, $state
		, $country
	;
	
	public function toArray( $params = array() ){
		$args = array(
			'include' => array(
				'id'
				, 'company'
				, 'name_first'
				, 'name_last'
				, 'street_address'
				, 'postal_code'
				, 'city'
				, 'state'
				, 'country'
			)
		);
		
		return parent::toArray($args);
	}
	
	public function load( $params = array() ){		
		$q = $this->dbQuery("
			SELECT	
				entry_company AS company,
				entry_firstname AS name_first,
				entry_lastname AS name_last,
				entry_street_address AS street_address,
				entry_postcode AS postal_code,
				entry_city AS city,
				entry_state AS state,
				entry_country_id AS country
			FROM
				address_book
			WHERE
				address_book_id = '" . $this->getId() . "'
		");
		
		if( $this->dbNumRows($q) < 1 ){
			throw new \Exception(
				"404: address book ID not set",
				404
			);
		}
		
		$this->setProperties($this->dbFetchArray($q));
	}
	
	public function setCompany( $string ){
		$this->company = (string)$string;
	}
	
	public function getCompany(){
		return $this->company;
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
	
	public function setStreetAddress( $string ){
		$this->streetAddress = (string)$string;
	}
	
	public function getStreetAddress(){
		return $this->streetAddress;
	}
	
	public function setPostalCode( $string ){
		$this->postalCode = (string)$string;
	}
	
	public function getPostalCode(){
		return $this->postalCode;
	}
	
	public function setCity( $string ){
		$this->city = (string)$string;
	}
	
	public function getCity(){
		return $this->city;
	}
	
	public function setState( $mixed ){
		// @todo: support multiple input types..
		$this->state = $mixed;
	}
	
	public function getState(){
		return $this->state;
	}
	
	public function setCountry( $mixed ){
		// @todo: support multiple input types..
		$this->country = $mixed;
	}
	
	public function getCountry(){
		return $this->country;
	}
}


