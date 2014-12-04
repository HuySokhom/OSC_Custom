<?php

namespace Aedea\Core\Module;

use Aedea\Core\Database\StdObjectPolymorphic;

class Object extends StdObjectPolymorphic {
	
	const
		TBL_MODULES = 'modules'
	;
	
	protected
		$sortOrder
	;
	
	public function load( $params = array() ){
		$q = $this->dbQuery("
			SELECT
				id,
				status,
				properties,
				created,
				modified
			FROM
				" . static::TBL_MODULES . "
			WHERE
				id = '" . $this->getId() . "'
		");

		if( ! $this->dbNumRows($q) ){
			throw new \Exception(
				"404: Module [{$this->getId()}] Not Found",
				404
			);
		}

		$r = $this->dbFetchArray($q);

		// set json encoded properties (which may vary because obj is
		// + polymorphic
		$this->properties = json_decode($r['properties'], true);
		unset($r['properties']);

		$this->setProperties($r);
	}

	public function insert( $params = array() ){
		if( ! isset($this->moduleType) ){
			throw new \Exception("Module Type Must Be Defined For Insertion");
		}
		
		$this->dbQuery("
			INSERT INTO
				" . static::TBL_MODULES . "
			(
				type,
				code,
				object_type,
				status,
				properties,
				sort_order,
				created
			)
				VALUES
			(
				'" . $this->dbEscape($this->moduleType) . "',
				'" . $this->dbEscape($this->moduleCode) . "',
				'" . $this->dbEscape(get_class($this)) . "',
				'" . (int)$this->getStatus() . "',
				'" . (
					json_encode($this->properties)
				) . "',
				'" . (int)$this->sortOrder . "',
				NOW()
			)
		");

		$this->setId($this->dbInsertId());
	}

	public function save(){
		$this->dbQuery("
			UPDATE
				" . static::TBL_MODULES . "
			SET
				status = '" . (int)$this->getStatus() . "',
				properties = '" . (
					json_encode($this->properties)
				) . "',
				sort_order = '" . (int)$this->sortOrder . "'
			WHERE
				id = '" . $this->getId() . "'
		");
	}

	public function saveStatus(){
		if( ! $this->getId() ){
			throw new \Exception(
				"404: Module [id] Not Set",
				404
			);
		}

		$this->dbQuery("
			UPDATE
				" . static::TBL_MODULES . "
			SET
				status = '" . (int)$this->getStatus() . "'
			WHERE
				id = '" . $this->getId() . "'
		");
	}

	public function getObjectType(){
		$q = $this->dbQuery("
			SELECT
				object_type
			FROM
				" . static::TBL_MODULES . "
			WHERE
				id = '" . $this->getId() . "'
		");

		if( ! $this->dbNumRows($q) ){
			throw new \Exception(
				"404: Module [{$this->getId()}] Not Found",
				404
			);
		}

		return $this->dbFetchArray($q)['object_type'];
	}	
	
	public function setSortOrder( $int ){
		$this->sortOrder = (int)$int;
	}
	
	public function getSortOrder(){
		return $this->sortOrder;
	}
}
