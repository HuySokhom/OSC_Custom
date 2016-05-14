<?php

/**
 * 
 * @author evan
 *
 * @todo: further abstraction for mysqli, mssql, etc
 */
namespace Aedea\Core\Database;

abstract class StdClass {
	
	protected 
		$dbRes
	;
	
	public function __construct( $params = array() ){
		// if no database resource is set, set from \RestApiResources
		if( ! $this->dbRes ){
			$this->setDbRes(
				\RestApiResources::getDbRes()
			);
		}
	}
	
	public function setDbRes( $mysql_link ) {
// 		if( !is_resource($mysql_link) ) {
// 			throw new \Exception(" invalid database resource ");
// 		}	
		
		$this->dbRes = $mysql_link;
		
		// ensure UTF8
		$this->dbQuery("SET NAMES 'utf8'");
	}
	
	public function getDbRes() {
		return $this->dbRes;
	}
	 
	// ---------------------------------------------------------
	// database function wrappers ---------------------
	// -------------------------------------
	
	protected function dbQuery( $query ) {	
		
		//echo "status of dbRes: " . is_resource($this->dbRes) . "<br />";
		
		// for debug:
		$query = mysqli_query( $this->dbRes, $query ) or die( mysqli_error( $this->dbRes ) );
		return $query;
		
		//return mysql_query( $query, $this->dbRes ) or die( mysql_error( $this->dbRes ) );
	}
	
	protected function dbFetchArray( $result, $result_type = MYSQLI_ASSOC ) {
		return mysqli_fetch_array( $result, $result_type);
	}
	
	protected function dbNumRows( $result ) {
		return mysqli_num_rows( $result );
	}
	
	protected function dbEscape( $mixed ) {
		if( is_array( $mixed ) ) {
			foreach( $mixed as $key => $val ) {
				$mixed[$key] = $this->dbEscape( $val );
			} 
			
			return $mixed;
		} else {
			
			// convert "Smart" characters to standard characters
			if( is_string($mixed) ){
				$mixed = \Util::convertSmartQuotes($mixed);
			}
			
			return mysqli_real_escape_string( $this->dbRes, $mixed );
		}
	}
	
	protected function dbInsertId() {
		return mysqli_insert_id( $this->dbRes );
	}

	/**
	 * Exxpected Params:
	 * 	- (string) table
	 * 	- (array) data
	 * 	
	 * Optional Params:
	 * 	- (string) action | default: insert
	 * 	- (params)
	 */
	protected function dbPerform( $params = array() ){
		$table = $params['table'];
		$data = $params['data'];
		$action = (
			isset($params['action'])  
				?
			strtoupper($params['action'])
				: 
			'INSERT'
		);
		$parameters = $params['params'] ?: '';
			
		reset($data);
		if( $action == 'INSERT' ){
			$query = 'INSERT INTO ' . $table . ' (';
			while (list($columns, ) = each($data)) {
				$query .= $columns . ', ';
			}
			$query = substr($query, 0, -2) . ') VALUES (';
			reset($data);
			while (list(, $value) = each($data)) {
				switch( strtoupper($value) ){
					case 'NOW()':
						$query .= 'NOW(), ';
						break;
					case 'NULL':
						$query .= 'NULL, ';
						break;
					default:
						$query .= '\'' . $this->dbEscape($value) . '\', ';
						break;
				}
			}
			$query = substr($query, 0, -2) . ')';
		} elseif( $action == 'UPDATE' ){
			$query = 'UPDATE ' . $table . ' SET ';
			while (list($columns, $value) = each($data)) {
				switch( strtoupper($value) ){
					case 'NOW()':
						$query .= $columns . ' = NOW(), ';
						break;
					case 'NULL':
						$query .= $columns .= ' = NULL, ';
						break;
					default:
						$query .= $columns . ' = \'' 
							. $this->dbEscape($value) 
							. '\', '
						;
						break;
				}
			}
			$query = substr($query, 0, -2) . ' WHERE ' . $parameters;
		} else {
			throw new \Exception(
				"unrecognised action [$action]"
			);
		}
	
		return $this->dbQuery($query);
	}
	
	
	
}
