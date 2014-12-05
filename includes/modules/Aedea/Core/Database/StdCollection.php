<?php

/**
 * collection object for loadable database objects (use with classes inherited from DatabaseLoadableObject)
 * @author Evan
 *
 */

namespace Aedea\Core\Database;

use 
	Aedea\Core\Database as Database
	, Aedea\Util\String
;

abstract class StdCollection extends Database\StdClass {
	
	protected
		$elements = array(),
		$tableList = array(),
		$whereList = array(),
		$orderByList = array(),
		$limit,
		$idField, // name of the field that represents the id (primary auto-increment key) of the row that the object is held in
		$selectFields = array(),
		$objectType, // type of object (descendant of DatabaseLoadableObject)
		$objectLoader, // name of the load method to use for the object
		$joinList = array(), // list of joins
		$distinct // boolean, whether or not the select is distinct
	;
	
	
	// TODO: these should be enabled once we have a compatible php version
	// ... some versions of php do not like these declarations if the second argument in child class is optional
// 	abstract public function setFilter( $filter_name, $filter_arg );
// 	abstract public function setOrderBy( $order_by_name, $order_by_arg );

	
	/**
	 * Optional Params:
	 * - (resource) db_link
	 */
	public function __construct( $params = array() ){
		foreach( $params as $param => $val ){
			switch( $param ){
				case 'db_link':
					$this->setDbRes($val);
					break;
			}
		}
		
		parent::__construct($params);
	}
	
	
	/**
	 * populates the collection with objects based on the filters / etc given
	 */
	public function populate() {
		
		// check that idField is set
		if( strlen($this->idField) < 1 ) {
			throw new \Exception(" populate method requires idField to be set ");
		}
		
		
		
		$qs = ''; // query string
		
		// "select"
		$qs .= ' SELECT ';
		
		// "distinct"
		if( $this->distinct ) {
			$qs .= ' DISTINCT ';
		}
		
		// "field"
		$qs .= $this->idField;
		$qs .= ' AS id ';
		
		// "additional fields"
		if( sizeof($this->selectFields) > 0 ) {
			foreach( $this->selectFields as $field ) {
				$qs .= ', ' . $field . ' ';
			}
		}
		
		// "from"
		$qs .= ' FROM ';
				
		// "tables"
		//$qs .= implode( ', ' , $this->tableList );
		$i = (int)0;
		foreach( $this->tableList as $table ) {
			$i++;
			
			$qs .= ' ' . $table . ' ';
			
			$joins = $this->getJoinsByJoinedTable( $table );
			if( sizeof( $joins ) > 0 ) {
				foreach( $joins as $join ) {
					$qs .= ' ' . $join['join_type'] . ' JOIN ';
					$qs .= ' ' . $join['table'] . ' ';
					$qs .= ' ON ' . $join['on_conditions'] . ' ';				
				}
			}
			
			
			if( $i < sizeof( $this->tableList) ) {
				$qs .= ' , ';
			}
		}
		unset( $i );
		
		
		// "where"
		if( sizeof($this->whereList) > 0 ) {
			$qs .= ' WHERE ';
			$qs .= implode( ' AND ', $this->whereList );
		} 
				
		// "order by"
		if( sizeof($this->orderByList) > 0 ) {
			$qs .= ' ORDER BY ';
			$qs .= implode( ', ', $this->orderByList );
		}
			
		// "limit"
		if( $this->limit ) {
			$qs .= ' LIMIT ';
			$qs .= $this->limit;
		}
		
		
		// build query
		$query = $this->dbQuery( $qs );
		
		
		// if query has no results, return
		if( !$this->dbNumRows( $query ) ) {
			return;
		}
		
		
		// debug:
//		echo "\n <br /> query string: $qs <br /> \n";
		
		
		// populate objects, with ids based on query results
		while ( $result = $this->dbFetchArray( $query ) ) {
			
			$object = new $this->objectType;
			$object->setDbRes( $this->dbRes );
			$object->setId( $result['id'] );
			
			// check to see if object is polymorphic, in which case we need to
			// create an instance of the polymorphic object
			if( 
				in_array(
					__NAMESPACE__ . '\StdObjectPolymorphic',
					class_parents($this->objectType)
				)
			){
				$polymorphic_object_type = $object->getObjectType();
				
				$object = new $polymorphic_object_type;
				$object->setDbRes( $this->dbRes );
				$object->setId( $result['id'] );
			}
			
					
			// if load method is set, call it
			if( $this->objectLoader ) {
				//echo $this->objectLoader;
				
				$objectLoaderMethod = $this->objectLoader;
				$object->$objectLoaderMethod();
			}
			
			$this->elements[] = $object;
			
		}
		
	}
	
	/**
	 * returns the current query string for the populate method
	 */
	public function getQueryString() {
		// check that idField is set
		if( strlen($this->idField) < 1 ) {
			throw new \Exception(" method requires idField to be set ");
		}
		
		
		$qs = ''; // query string
		
		// "select"
		$qs .= ' SELECT ';
		
		// "distinct"
		if( $this->distinct ) {
			$qs .= ' DISTINCT ';
		}
		
		// "field"
		$qs .= $this->idField;
		$qs .= ' AS id ';
		
		// "additional fields"
		if( sizeof($this->selectFields) > 0 ) {
			foreach( $this->selectFields as $field ) {
				$qs .= ', ' . $field . ' ';
			}
		}
		
		// "from"
		$qs .= ' FROM ';
		
		// "tables"
		//$qs .= implode( ', ' , $this->tableList );
		$i = (int)0;
		foreach( $this->tableList as $table ) {
			$i++;
		
			$qs .= ' ' . $table . ' ';
		
			$joins = $this->getJoinsByJoinedTable( $table );
			if( sizeof( $joins ) > 0 ) {
				foreach( $joins as $join ) {
					$qs .= ' ' . $join['join_type'] . ' JOIN ';
					$qs .= ' ' . $join['table'] . ' ';
					$qs .= ' ON ' . $join['on_conditions'] . ' ';
				}
			}
		
		
			if( $i < sizeof( $this->tableList) ) {
				$qs .= ' , ';
			}
		}
		unset( $i );
		
		
		// "where"
		if( sizeof($this->whereList) > 0 ) {
			$qs .= ' WHERE ';
			$qs .= implode( ' AND ', $this->whereList );
		}
		
		// "order by"
		if( sizeof($this->orderByList) > 0 ) {
			$qs .= ' ORDER BY ';
			$qs .= implode( ', ', $this->orderByList );
		}
		
		// "limit"
		if( $this->limit ) {
			$qs .= ' LIMIT ';
			$qs .= $this->limit;
		}		
		
		return $qs;
	}
	
	/**
	 * gets the number of rows the query returns (unlimited)
	 */
	public function getTotalCount() {
			// check that idField is set
		if( strlen($this->idField) < 1 ) {
			throw new \Exception(" getTotalCount method requires idField to be set ");
		}
		
		
		
		$qs = ''; // query string
		
		// "select"
		$qs .= ' SELECT ';
		
		// "distinct"
		if( $this->distinct ) {
			$qs .= ' DISTINCT ';
		}
		
		// "field"
		$qs .= $this->idField;
		$qs .= ' AS id ';
		
		// "from"
		$qs .= ' FROM ';
				
		// "tables"
		//$qs .= implode( ', ' , $this->tableList );
		$i = (int)0;
		foreach( $this->tableList as $table ) {
			$i++;
			
			$qs .= ' ' . $table . ' ';
			
			$joins = $this->getJoinsByJoinedTable( $table );
			if( sizeof( $joins ) > 0 ) {
				foreach( $joins as $join ) {
					$qs .= ' ' . $join['join_type'] . ' JOIN ';
					$qs .= ' ' . $join['table'] . ' ';
					$qs .= ' ON ' . $join['on_conditions'] . ' ';				
				}
			}
			
			
			if( $i < sizeof( $this->tableList) ) {
				$qs .= ' , ';
			}
		}
		unset( $i );
		
		
		// "where"
		if( sizeof($this->whereList) > 0 ) {
			$qs .= ' WHERE ';
			$qs .= implode( ' AND ', $this->whereList );
		} 
				
		
		// build query
		$query = $this->dbQuery( $qs );
		
		
		// return number of rows
		return $this->dbNumRows( $query );
				
	}
	
	
	public function getElements() {
		return $this->elements;
	}
	
	public function setElements( $array ){
		$this->elements = (array)$array;
	}
	
	public function getElementsAsArray( $params = array() ){
		$array = array();
		
		foreach( $this->elements as $element ){
			$element->load();
			
			$array[] = $element->toArray($params);
		}
		
		return $array;
	}
	
	public function getFirstElement() {
		foreach( $this->elements as $element ) {
			return $element;
		}
		
		return false;
	}
	
	
	public function setDistinct( $boolean ) {
		$this->distinct = $boolean ? true : false;
	}

	
	public function setFilter( $filter_name, $filter_arg ){
		$method_name = 'filterBy' . ucfirst(
			\Util::snakeToCamel($filter_name)
		);
		$filter_arg = $this->dbEscape( $filter_arg );
	
		if( method_exists($this, $method_name) ){
			call_user_func_array(
				array(
					$this,
					$method_name
				),
				array(
					$filter_arg
				)
			);
		} else {
			throw new \Exception(
				"method [$method_name] does not exist"
			);
		}
	}
	
		
	/**
	 * sets the limit of the collection
	 * - one argument: limit
	 * - two arguments: offset, limit
	 * 	
	 */
	public function setLimit( $arg1, $arg2 = false ) {
		
		// support comma separated
		if( strpos( $arg1, ',' ) ) {
			$pieces = explode(',', $arg1);
			$this->limit = (int)$pieces[0] . ',' . (int)$pieces[1]; 
			return;
		}
		
		// support double argument
		if( !$arg2 ) {
			$this->limit = (int)$arg1;
		} else {
			$this->limit = (int)$arg1 . ',' . (int)$arg2;
		}
		
	}
	
	/**
	 * adds a select field (does not add duplicates)
	 * @param (string) $fieldname
	 * @param (string) $alias
	 */
	protected function addSelectField( $fieldname, $alias = false ) {
		$fieldname = trim($fieldname);
		$alias = trim($alias);
		
		if( strlen($alias) > 0 ) {
			$fieldname = $fieldname . ' ' . $alias;
		}
		
		if( !in_array($fieldname, $this->selectFields) ) {
			$this->selectFields[] = $fieldname;
		}
	}
	
	
	/**
	 * adds a tablename to tableList
	 * - does not add duplicates
	 */
	protected function addTable( $tablename, $alias = false ) {
		$tablename = trim($tablename);
		$alias = trim($alias);
		
		if( strlen($alias) > 0 ) {
			$tablename = $tablename . ' ' . $alias; 
		} 
		
		if( !in_array($tablename, $this->tableList) ) {
			$this->tableList[] = $tablename;
		}	
	}
	
	/**
	 * sets the object type (should be a descendant of DatabaseLoadableObject)
	 * @param (string) $class_name
	 */
	protected function setObjectType( $class_name ) {
		
		$testObject = new $class_name;
		
		if( in_array( 'DatabaseLoadableObject', class_parents($testObject) ) ) {			
			$this->objectType = $class_name;
			unset( $testObject );
			return;	
		} 
		
		throw new \Exception(" object type must be a descendant of DatabaseLoadableObject ");
		
	}
	
	/**
	 * sets the loader for the object type of the collection
	 * @param unknown_type $method_name
	 */
	public function setLoader( $method_name ) {
		
		if( !$this->objectType ) {
			throw new \Exception(" object type must be set before loader ");
		}
		
		$object = new $this->objectType;
		$reflector = new ReflectionObject($object);
		
		if( $reflector->hasMethod($method_name) ) {
			$this->objectLoader = $method_name;
		}
		
	}
	
	/**
	 * adds a where string to be used in the query
	 * - does not add duplicate strings
	 */
	protected function addWhere( $where_str ) {
		$where_str = trim( $where_str );
		
		if( !in_array( $where_str, $this->whereList) ) {
			$this->whereList[] = $where_str;
		}
		
	}
	
	
	/**
	 * sets an "order by" param from this.sorters
	 * @param (string) $sort_by
	 */
	public function setSortBy( $sort_by ){
		$sort_by = trim($sort_by);
			
		// detect direction
		$direction = 'ASC';
		if( String::endsWith($sort_by, '_desc') ){
			$direction = 'DESC';
			$sort_by = substr($sort_by, 0, -5);
		}
					
		else if( String::endsWith($sort_by, '_asc') ){
			$sort_by = substr($sort_by, 0, -4);
		}
		
		$method_name = 'sortBy' . ucfirst(
			\Util::snakeToCamel($sort_by)
		);
		
		if( method_exists($this, $method_name) ){
			call_user_func_array(
				array(
					$this,
					$method_name
				),
				array(
					$direction
				)
			);
		} else {
			throw new \Exception(
				"method [$method_name] does not exist"
			);
		}
		
	}
	
	
	/**
	 * adds "order by" claus to be used in query
	 * - does not add duplicates
	 */
	protected function addOrderBy( $field, $direction = 'ASC' ) {
		$field = trim( $field );
		$direction = trim( $direction );
		
		$order_by_str = $field . " " . $direction;
		
		if( !in_array( $order_by_str, $this->orderByList ) ) {
			$this->orderByList[] = $order_by_str;
		} 
		
	}
	
	/**
	 * adds a join table to the query (must target an existing table, with alias)
	 * @param (string) $join_type - LEFT, RIGHT, INNER
	 * @param (string) $joined_table
	 * @param (string) $joined_table_alias
	 * @param (string) $table
	 * @param (string) $table_alias
	 * @param (string) $on_conditions
	 * 
	 * TODO: possibly support joining to join tables (if necessary)
	 */
	protected function addJoin( $join_type, $joined_table, $joined_table_alias, $table, $table_alias, $on_conditions ) {
		
		$join_type = trim( strtolower($join_type) );
		$joined_table = trim( $joined_table );
		$joined_table_alias = trim( $joined_table_alias );
		$table = trim( $table );
		$table_alias = trim( $table_alias );
		$on_conditions = trim( $on_conditions );
		
		// check that $join_type is valid
		if ( 
			$join_type != 'left' 
				&& 
			$join_type != 'right' 
				&&
			$join_type != 'inner' 
		) {
			throw new \Exception(" unrecognised join type [$join_type] specified ");
		}
		
		
		// check that $joined_table is present in $this->tableList 
		if( strlen( $joined_table_alias ) > 0 ) {
			$joined_table = $joined_table . ' ' . $joined_table_alias;	
		}
		
		if( !in_array($joined_table, $this->tableList) ) {
			throw new \Exception(" joined table [$joined_table] does not exist in tableList ");	
		}
		
		
		// add the join to the joinList if it's not there already
		if( strlen( $table_alias ) > 0 ) {
			$table = $table . ' AS ' . $table_alias;
		}
		
		if( !$this->joinExists( $join_type, $joined_table, $table ) ) {
			$this->joinList[] = array(
				'join_type' => $join_type,
				'joined_table' => $joined_table,
				'table' => $table,
				'on_conditions' => $on_conditions
			);
		}
		
		
	}
	
	
	/**
	 * helper method; lets us know if a join exists; returns true if join exists, false otherwise
	 * @param (string) $join_type
	 * @param (string) $joined_table
	 * @param (string) $table
	 * @return (bool)
	 */
	private function joinExists( $join_type, $joined_table, $table ) {
		foreach( $this->joinList as $j ) {
			if( 
				$j['join_type'] == $join_type
					&&
				$j['joined_table'] == $joined_table
					&&
				$j['table'] == $table
			) {
				return true;
			}
		}
		
		return false;
	}
	
	
	/**
	 * gets the joins that apply to a given table ($joined_table)
	 * @param (string) $joined_table
	 * @return (array)
	 */
	private function getJoinsByJoinedTable( $joined_table ) {
		$joins = array();
		
		foreach( $this->joinList as $join ) {
			if( $join['joined_table'] == $joined_table ) {
				$joins[] = $join;
			}
		}
		
		return $joins;
	}
	
	
	/**
	 * 
	 * filter by id
	 */
	protected function filterById( $int ){
		$this->addWhere($this->idField . " = '" . (int)$int . "'");
	}
		
}

