<?php

/**
 * useful for objects that use database resources
 * - includes database resource property with setter
 * @author Evan
 *
 */
abstract class DatabaseObject {
	
	protected 
		$dbRes
	;
	
	public function setDbRes( $mysql_link ) {
		if( !is_resource($mysql_link) ) {
			throw new Exception(" invalid database resource ");
		}	
		
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
		$query = mysql_query( $query, $this->dbRes ) or die( mysql_error( $this->dbRes ) );
		return $query;
		
		//return mysql_query( $query, $this->dbRes ) or die( mysql_error( $this->dbRes ) );
	}
	
	protected function dbFetchArray( $result, $type = MYSQL_ASSOC ) {
		return mysql_fetch_array( $result, $type );
	}
	
	protected function dbNumRows( $result ) {
		return mysql_num_rows( $result );
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
				$mixed = Util::convertSmartQuotes($mixed);
			}
			
			return mysql_real_escape_string( $mixed, $this->dbRes );
		}
	}
	
	protected function dbInsertId() {
		return mysql_insert_id( $this->dbRes );
	}	
}
