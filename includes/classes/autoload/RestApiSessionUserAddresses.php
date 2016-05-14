<?php

use 
	Aedea\Catalog\Address\Collection as AddressCol
;

class RestApiSessionUserAddresses extends RestApi {

	public function get( $params = array() ){
		// legacy support
		if( isset($params['GET']) ){
			$params = $params['GET'];
		}
		
		$col = new AddressCol();
		
		$this->applyFilters($col, $params);
		$this->applySortBy($col, $params);
		$this->applyLimit($col, $params);
		
		return $this->getReturn($col, $params);
	}
	
}
