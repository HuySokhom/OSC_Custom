<?php

namespace Aedea\Catalog\User\Customer;

use 
	Aedea\Catalog\User\Object as User
	, Aedea\Catalog\Address\Object as Address
;

class Object extends User {
			
	public function toArray( $params = array() ){
		$args = array(
			'include' => array(
				'id',
				'name_first',
				'name_last',
				'email',
				'addresses',
				'address_shipping_default',
				'address_billing_default',
			)
		);
	
		return parent::toArray($args);
	}
	
	public function __construct( $params = array() ){
		parent::__construct($params);
	}
	
	public function load( $params = array() ){
		// @todo
	}
	
	public function getAddresses(){
		// @todo: implement
		return array();
	}
	
	public function getAddressShippingDefault(){
		$elements = (new \RestApi())->getInstance(array(
			'request' => 'Session/User/Addresses'
		))->get(array(
			'filters' => array(
				'customer_id_default_shipping_address' => $this->getId(),
			),
		))['elements'];
			
		if( sizeof($elements) > 0 ){
			return $elements[0];
		} else {
			return (new Address())->toArray();
		}
	}
	
	public function getAddressBillingDefault(){
		$elements = (new \RestApi())->getInstance(array(
			'request' => 'Session/User/Addresses'
		))->get(array(
			'filters' => array(
				'customer_id_default_billing_address' => $this->getId(),
			),
		))['elements'];
			
		if( sizeof($elements) > 0 ){
			return $elements[0];
		} else {
			return (new Address())->toArray();
		}
	}
	
}
