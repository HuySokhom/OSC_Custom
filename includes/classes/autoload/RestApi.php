<?php

use \Aedea\Core\Database\StdCollection as DbCollection;

class RestApi {
	
	private 
		$id,
		$owner
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
		
		// if resource starts with "api/", strip it
		if( strpos($params['request'], 'api/' ) === 0 ){
			$params['request'] = substr($params['request'], 4);
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
	
	
	
	/*
	 * 	convenience methods
	 */
	protected function getReturn( DbCollection $col, $params = array() ){
		// decide what we're returning..
		$return = isset($params['return'])
				?
			$params['return']
				:
			false
		;
	
		switch( $return ){
			case 'count':
				return $this->getReturnCount($col);
	
			default:
				return $this->getReturnDefault($col, $params);
		}
	}
	
	protected function getReturnCount( DbCollection $col ){
		return array(
			'data' => array(
				'count' => $col->getTotalCount()
			)
		);
	}
	
	protected function getReturnDefault( DbCollection $col, $params ){
		$col->populate();
	
		return array(
			'data' => array(
				'elements' => $col->getElementsAsArray($params),
			)
		);
	}

	public function applyFilters( DbCollection $col, $params = array() ){
		// support singular requests..
		if( $this->getId() ){
			$params['filters']['id'] = $this->getId();
		}
		
		if( is_array($filters = $params['filters']) ){
			foreach( $filters as $k => $v ){
				$col->setFilter($k, $v);
			}
		}
	}
	
	
	public function applySortBy( DbCollection $col, $params = array() ){
		if( is_array($sort_bys = $params['sort_by']) ){
			foreach( $sort_bys as $sort_by ){
				$col->setSortBy($sort_by);
			}
		}
		
		else if( is_string($sort_by = $params['sort_by']) ){
			$col->setSortBy($sort_by);
		}
	}
	
	public function applyLimit( DbCollection $col, $params = array() ){
		if( is_array($limit = $params['limit']) ){
			if( sizeof($limit) == 1 ){
				$col->setLimit($limit[0]);
			} else {
				$col->setLimit($limit[0], $limit[1]);
			}
		}
		
		elseif(
			is_numeric($limit)
				&&
			$limit < 100
		){
			$col->setLimit($limit);
		}
		
		else {
			$col->setLimit(100);
		}
	}
	
}




