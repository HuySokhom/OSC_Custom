<?php
class oscImageCollection extends DatabaseLoadableObjectCollection {
	
	public function __construct() {
		$this->addTable('image_slider');
		$this->idField = 'id';
		$this->setObjectType('oscImage');
		
	}	
	
	
	/**
	 * sets a filter (inclusive)
	 * - use setFilterExclusive for exclusive filters
	 * @param (string) $filter_name
	 * @param (mixed) $filter_arg 
	 */
	public function setFilter( $filter_name, $filter_arg = false ) {
		
		$filter_name = strtolower( $filter_name );
		$filter_arg = $this->dbEscape( $filter_arg );
	
		switch( $filter_name ) {
			// @todo set filter what u want
			case 'id':
				$this->filterById( $filter_arg );
				break;
		}
	}
	
	/**
	 * filter by id
	 * @param (int) $arg
	 */
	protected function filterById( $arg ) {
		$arg = (int)$arg;
		$this->addWhere(" id = " . $arg . " ");
	}
	
}