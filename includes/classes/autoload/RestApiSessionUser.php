<?php

use
	Aedea\Catalog\User\Customer\Collection as CustomerCol
	, Aedea\Catalog\User\Customer\Object as Customer
;

class RestApiSessionUser extends RestApi {
	
	public function __construct( $params = array() ){
		parent::__construct($params);
	}
	
	public function get( $params = array() ){
		// if user is not set, check for user by session id;
		// + provide an empty user object if no matching user found..
		if( ! isset( $_SESSION['user']) ){
			$col = new CustomerCol;
			
			// this is the session's user, so filters are immutable
			$this->applyFilters($col, array(
				'id' => $this->getOwner()->getId(),
				'status' => 1
			));
			
			$_SESSION['user'] = (
				$col->getTotalCount() > 0
					?
				// can only ever be one element returned
				$_SESSION['user'] = $this->getReturn(
					$col,
					$params
				)['elements'][0]
					:
				(new Customer)->toArray()
			);
		}	
		
		return array(
			'data' => $_SESSION['user']
		);
	}
 	
 	public function patch( $params = array() ){
 		// @todo
 	}
 	
 	public function post( $params = array() ){
 		// @todo
 	}
	
}
