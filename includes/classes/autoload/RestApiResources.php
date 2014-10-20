<?php

class RestApiResources {
	
	private function __construct() {
		// singleton
	}
	
	static private 
		$dbRes
	;
	
	static public function getDbRes() { 
		return self::$dbRes;
	}
	
	static public function setDbRes( $dbRes ) {
		if( !is_resource( $dbRes ) ) {
			throw new Exception("not a resource");
		}
		
		self::$dbRes = $dbRes;
	}
	
	/**
	 * 
	 * Expected Parameters:
	 * - (RestApi) $obj
	 * - (array) $req // request
 	 */
	static public function cache_exists( $params ) {
		
		$obj = $params['obj'];
		$req = $params['req'];

		
		$reqs = serialize($req); // request serialized
		$h = hash('sha1', $reqs); // hash
		
		$q = mysql_query("
			SELECT
				id
			FROM	
				REST_cache
			WHERE
				resource = '" . get_class( $obj ) . "'
					AND
				hash = '" . mysql_real_escape_string( $h, self::getDbRes() ) . "'		
		", self::getDbRes());
		
		if( mysql_num_rows( $q ) > 0 ) {
			$r = mysql_fetch_assoc( $q );
			return $r['id'];	
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * Expected Parameters:
	 * - (RestApi) $obj
	 * - (array) $req
	 * - (mixed) $data
	 * 
	 */
	static public function cache_create( $params ) {
		
		$obj = $params['obj'];
		$req = $params['req'];
		$data = $params['data'];
		
		$reqs = serialize($req); // request serialized
		$h = hash('sha1', $reqs); // hash
		
// 		if( get_class($params['obj']) == 'RestApiProductsProducts' ) {
// 			print_r( $params ); exit;
// 		}

// 		echo mysql_real_escape_string( $reqs, self::getDbRes() );
// 		echo "\n\n\n";		

		
		mysql_query("
			INSERT INTO
				REST_cache
			(
				resource,
				hash,
				request,
				data,
				created,
				modified
			)
				VALUES
			(
				'" . get_class( $obj ) . "',
				'" . mysql_real_escape_string( $h, self::getDbRes() ) . "',
				'" . mysql_real_escape_string( $reqs, self::getDbRes() ) . "',
				'" . mysql_real_escape_string( serialize($data), self::getDbRes() ) . "',
				NOW(),
				NOW()
			)
		", self::getDbRes()) or die( mysql_error() );
				
		$insert_id = mysql_insert_id( self::getDbRes() );
		
		$q = mysql_query(" select request from REST_cache where id = " . (int)$insert_id);
		$r = mysql_fetch_assoc( $q );
		
// 		echo $r['request'];
// 		exit;
		
		
		
		return mysql_insert_id( self::getDbRes() );
	}
	
	/**
	 * 
	 * Expected Parameters:
	 * - (RestApi) $obj
	 * 
	 * Optional Parameters:
	 * - (int) $max_age // in seconds
	 */
	static public function cache_load( $params ) {
		
		$obj = $params['obj'];
		if( isset($params['max_age']) ) {
			$max_age = $params['max_age'];
		}
		
		// if max age is set, check to see if expired
		$expired = false;
		if( isset($max_age) ) {
			$q = mysql_query("
				SELECT
					id
				FROM
					REST_cache
				WHERE
					id = '" . (int)$obj->getId() . "'
						AND
					UNIX_TIMESTAMP( modified ) + '" . (int)$max_age . "' <= UNIX_TIMESTAMP( NOW() )	
			", self::getDbRes());
			
			if( mysql_num_rows( $q ) > 0 ) {
				$expired = true;
			}
		}
		
// 		if( get_class( $obj ) == 'RestApiProductsProducts' ) {
// 			print_r( $params );
// 			echo "expired is $expired";
// 			exit(' params!! ');
// 		}
		
		// if expired, then refresh the cache and return the new data
		// - doing things this way avoids having to deal with $data twice, and should be more efficient
		if( $expired ) { 			
						
			$q = mysql_query("
				SELECT
					request 
				FROM
					REST_cache
				WHERE
					id = '" . (int)$obj->getid() . "'		
			", self::getDbRes());
			$r = mysql_fetch_assoc( $q );
						
// 			if( get_class( $obj ) == 'RestApiProductsCategories' ) {
// 				print_r( unserialize( $r['request'] ) ); exit;
// 			}

// 			echo( $r['request'] );
// 			exit( '  |||| zzz ');
			

			$data = $obj->post(array(
				'request' => unserialize( $r['request'] )
			));

// 			if( get_class( $obj ) == 'RestApiProductsProducts' ) {
// 				print_r( $data );
// 				exit(' expird!!! ');
// 			}
			
			return $data;
			
		} else {
			// return the data from cache
			$q = mysql_query("
				SELECT
					data 
				FROM	
					REST_cache
				WHERE
					id = '" . (int)$obj->getId() . "'
			", self::getDbRes()) or die( mysql_error() );
			$r = mysql_fetch_assoc( $q );
			
			$data = unserialize( $r['data'] );
						
// 			if( get_class( $obj ) == 'RestApiProductsProducts' ) {
					
// 				print_r( $data );
// 				exit(' from cacheee!!! ' . get_class($obj) . ' ' . $obj->getId() . ' /parent: ' . get_class($obj->getOwner()) . $obj->getOwner()->getId() );
// 			}
			
			return $data;
		}
	}
	
	/**
	 * 
	 * Expected Parameters:
	 * - (int) id // cache id
	 */
	static public function cache_get_request( $params ) {
		$q = mysql_query("
			SELECT
				request
			FROM
				REST_cache
			WHERE
				id = '" . (int)$params['id'] . "'
		", self::getDbRes());
		$r = mysql_fetch_assoc( $q );
		
		return unserialize( $r['request'] );
	}
	
	/**
	 * 
	 * Expected Parameters:
	 * - (RestApi) obj
	 * - (mixed) data
	 */
	static public function cache_update( $params ) {
		
// 		if( get_class($params['obj']) == 'RestApiProductsCatagories' ) {
// 			print_r( $params ); exit;
// 		}

// 		if( get_class($params['obj']) == 'RestApiProductsProducts' ) {
// 			print_r( $params ); exit;
// 		}
		
		$q = mysql_query("
			UPDATE
				REST_cache
			SET
				data = '" . mysql_real_escape_string( serialize( $params['data'] ), self::getDbRes() ) . "' 
			WHERE
				id = '" . (int)$params['obj']->getId() . "'
		");
	}
	
		
}

