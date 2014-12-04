<?php

class RestApiSession extends RestApi {
	
	public function __construct( $params = array() ){	
		parent::__construct($params);
		
		// hard set id to session customer id..
		$this->setId(
			isset($_SESSION['customer_id']) 
				?
			$_SESSION['customer_id']
				:
			0
		);
	}
	
	public function get( $params = array() ){	
		$data = array();
		
		// cart from session..
		$data['cart'] = (new \RestApi)->getInstance(array(
			'request' => 'Cart'
		))->get()['data'];
		
		// user
		$data['user'] = (new \RestApi)->getInstance(array(
			'request' => 'Session/User'
		))->get()['data'];
	
		return array(
			'data' => $data
		);
	}
}
