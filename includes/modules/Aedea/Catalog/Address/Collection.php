<?php

namespace Aedea\Catalog\Address;

use Aedea\Core\Database\StdCollection;

class Collection extends StdCollection {
	
	public function __construct( $params = array() ){
		parent::__construct($params);
		
		$this->addTable('address_book', 'ab');
		$this->idField = 'ab.address_book_id';
		$this->setDistinct(true);
		
		$this->objectType = 'Aedea\Catalog\Address\Object';		
	}
	
	public function filterByCustomerId( $arg ){
		$this->addWhere("ab.customers_id = '" . (int)$arg . "'");
	}
	
	public function filterByCustomerIdDefaultShippingAddress( $arg ){
		$this->addTable('customers', 'c');
		
		$this->addWhere("c.customers_id = '" . (int)$arg . "'");
		$this->addWhere(
			"c.customers_default_shipping_address_id = ab.address_book_id"
		);
	}
	
	public function filterByCustomerIdDefaultBillingAddress( $arg ){
		$this->addTable('customers', 'c');
	
		$this->addWhere("c.customers_id = '" . (int)$arg . "'");
		$this->addWhere(
				"c.customers_default_address_id = ab.address_book_id"
		);
	}
}
