<?php

class RestApiUtil {
	
	/**
	 * encodes a string / array values to utf8
	 * @param (mixed) $mixed
	 */
	static function utf8Encode( $mixed ) {
		if( is_array( $mixed ) ) {
			foreach( $mixed as $key => $value ) {
				$mixed[$key] = self::utf8Encode( $value );
			}
		} else {
			if( !mb_check_encoding( $mixed, 'UTF-8') ) {
				$mixed = utf8_encode( $mixed );
			}
		}
	
		return $mixed;
	}

	/**
	 * 
	 * Expected Params:
	 * - (string) table
	 * - (array) data
	 * - (string) action
	 * 
	 * Optional Params:
	 * - (string) where
	 * 
	 * @return resource
	 */
	static function dbPerform( $params ){
		$table = $params['table'];
		$data = $params['data'];
		$action = strtolower($params['action']);	
		
		reset($data);
		if ($action == 'insert') {
			$query = 'insert into ' . $table . ' (';
			while (list($columns, ) = each($data)) {
				$query .= $columns . ', ';
			}
			$query = substr($query, 0, -2) . ') values (';
			reset($data);
			while (list(, $value) = each($data)) {
				switch ((string)$value) {
					case 'now()':
						$query .= 'now(), ';
						break;
					case 'null':
						$query .= 'null, ';
						break;
					default:
						$query .= '\'' . self::dbInput($value) . '\', ';
						break;
				}
			}
			$query = substr($query, 0, -2) . ')';
		} elseif ($action == 'update') {
			$query = 'update ' . $table . ' set ';
			while (list($columns, $value) = each($data)) {
				switch ((string)$value) {
					case 'now()':
						$query .= $columns . ' = now(), ';
						break;
					case 'null':
						$query .= $columns .= ' = null, ';
						break;
					default:
						$query .= $columns . ' = \'' . self::dbInput($value) . '\', ';
						break;
				}
			}
			$query = substr($query, 0, -2) . (
				isset($params['where'])
					?
				' where ' . $params['where']
					: 
				false
			);
		}
		
		return self::dbQuery(array(
			'query' => $query
		));
	}
	
	/**
	 * 
	 * Expected Params:
	 * - (string) query
	 * 
	 * @return resource
	 */
	static function dbQuery( $params ){
		return mysql_query(
			$params['query'], 
			RestApiResources::getDbRes()
		);
	}
	
	/**
	 * 
	 * @param (string) $value
	 * @return string
	 */
	static function dbInput( $value ){
		return mysql_real_escape_string( $value, RestApiResources::getDbRes() );
	}
	
	
	static function dbInsertId( $params = array() ){
		return mysql_insert_id( RestApiResources::getDbRes() );
	}
	
	/**
	 * 
	 * Expected Params:
	 * - (resource) query
	 */
	static function dbFetchAssoc( $params = array() ){
		return mysql_fetch_assoc( $params['query'] );
	}
	
	/**
	 * Expected Params:
	 * - (string | resource) query
	 * 
	 * @return (int) number of rows
	 */
	static function dbNumRows( $params = array() ){
		if( is_string($params['query']) ){
			return mysql_num_rows(self::dbQuery(array(
				'query' => $params['query']
			)));
		}
		
		elseif( is_resource($params['query']) ){
			return mysql_num_rows($params['query']);
		}
	}
	
	
}

