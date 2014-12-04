<?php

namespace Aedea\Core\Module;

use Aedea\Core\Database\StdCollection;

class Collection extends StdCollection {
	
	const 
		TBL_MODULES = 'modules'
	;
	
	public function __construct( $params = array() ){
		parent::__construct($params);
		
		$this->addTable(static::TBL_MODULES, 'm');
		$this->idField = 'm.id';
		$this->setDistinct(true);
		
		$this->objectType = 'Aedea\Core\Module\Object';		
	}
	
	public function filterByStatus( $arg ){
		$this->addWhere("m.status = '" . (int)$arg . "'");
	}
	
	public function filterByType( $arg ){
		$this->addWhere("m.type = '" . $this->dbEscape($arg) . "'");
	}
	
	public function filterByCode( $arg ){
		$this->addWhere("m.code = '" . $this->dbEscape($arg) . "'");
	}
}
