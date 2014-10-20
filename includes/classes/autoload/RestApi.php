<?php

class RestApi {
	
	private 
		$id,
		$owner
	;
	
	protected 
		$cacheLife = 0 // no cache by default
	;
	
	public function __construct( $params = false ) {
	
		if( $params['request'] ) {
			// grab first piece of the request
			if( strlen($request = trim(strstr($params['request'], '/'), '/')) > 0 ) {
				// php 5.2 compatible
				$pieces = explode('/', $params['request']);
	
				if( is_numeric($pieces[0]) ) {
					$this->setId( $pieces[0] );
					$params['request'] = $request;
				}
				unset( $pieces );
			} else {
				// if the request is numeric, then it's an id
				if( is_numeric($params['request']) ) {
					$this->setId( $params['request'] );
					$params['request'] = '';
				}
			}
		}
	}
	
	public function getInstance( $params ) {		
		if( is_object( $params['owner'] ) ) {
			$this->setOwner( $params['owner'] );
		}
		
		// if there is an inital "api/" in the request, remove it
		if( strpos($params['request'], 'api/') === 0 ) {
			$params['request'] = substr($params['request'], 4); // length of "api/" is 4
		}
		
		if( $params['request'] ) {
			if( strlen($request = trim(strstr($params['request'], '/'), '/')) > 0 ) {
				// php 5.2 compatible
				$pieces = explode('/', $params['request']);
				$class = $pieces[0];
				unset( $pieces );
			} else {
				$class = $params['request'];
			}
		
			$class = get_class($this) . $class;
		
			$instance = new $class(array('request' => &$request));	
			$instance->setOwner( $this );
			
			if( $request ) {
				return $instance->getInstance(array(
					'request' => $request
				));
			}
			
			return $instance;
		}
	}
		
	public function getOwner() {
		return $this->owner;
	}
	
	protected function setOwner( $owner ) {
		$this->owner = $owner;
	}

	public function getId() {
		return $this->id;
	}
	
	protected function setId( $id ) {
		$this->id = (int)$id;
	}
	
	
	##### CORE METHODS #####
	public function get( $params = false ) {
		$data = RestApiResources::cache_load(array(
			'obj' => $this,
			'max_age' => $this->cacheLife // 5 minutes
		));
	
		return array(
			'data' => $data,
			// 			'debug_mode' => true
		);
	}
	
	public function put( $params = false ) {
		$PUT = $params['request']; // for security reasons, we do not allow user PUTs by default..
		// create a custom PUT method and call the parent if this is necessary
	
		if( !isset( $PUT['data']) ) {
			throw new Exception("PUT.data must be set", 400);
		} 
		
		RestApiResources::cache_update(array(
			'obj' => $this,
			'data' => $PUT['data']
		));
	
	}
	
	
}




