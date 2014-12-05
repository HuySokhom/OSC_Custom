<?php

set_include_path('..');

require_once('includes/application_top.php');



// strip relative path from request
$request_alias = substr($_SERVER['REQUEST_URI'], strlen(DIR_WS_HTTP_CATALOG));
$request_alias_pieces = explode('?', $request_alias);
$request_file = $request_alias_pieces[0];


// remove "api/" from the $request_file string
$request_file = strstr($request_file, '/');
$request_file = trim($request_file, '/');


// support HTTP_X_HTTP_METHOD_OVERRIDE
$request_method;
if( isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) && $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) {
	$request_method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
} else {
	$request_method = $_SERVER['REQUEST_METHOD'];
}


switch( $request_method ) {
	case 'GET':
		try {
			$ApiObj = new RestApi();
			$ApiObj = $ApiObj->getInstance(array(
					'request' => $request_file
			));
			$r = $ApiObj->get(array(
					'GET' => $_GET
			));
				
			if( ! isset($r['debug_mode']) || ! $r['debug_mode'] ) {
				header('content-type: application/json');
			}
			echo json_encode( RestApiUtil::utf8Encode($r['data']) );
		} catch( Exception $e ) {
				
			print_r($e);exit;
			header('Error', true, $e->getCode() > 0 ? $e->getCode() : 400);
		}
		break;

	case 'POST':
		$raw_input = file_get_contents('php://input');
		$_POST = json_decode($raw_input, true);
			
		if( ! $_POST ){
			parse_str( $raw_input, $array );
			$_POST = $array;
			unset($array);
		}

		// 		$_POST = (array)$_POST;

		try {
			$ApiObj = new RestApi();
			$ApiObj = $ApiObj->getInstance(array(
					'request' => $request_file
			));
			$r = $ApiObj->post(array(
					'POST' => $_POST
			));

			if( !$r['debug_mode'] ) {
				header('content-type: application/json');
			}
			echo json_encode( RestApiUtil::utf8Encode($r['data']) );
		} catch( Exception $e ) {
			print_r($e);exit;
				
			header('Error', true, $e->getCode() > 0 ? $e->getCode() : 400);
		}
		break;


	case 'PATCH':
		$raw_input = file_get_contents('php://input');
		$_PATCH = json_decode($raw_input, true);
			
		if( ! $_PATCH ){
			parse_str( $raw_input, $array );
			$_PATCH = $array;
			unset($array);
		}

		//$_PATCH = (array)$_PATCH;

	case 'UPDATE':
		if( ! isset($_PATCH) ){
			// throw deprecation error
			trigger_error('use PATCH instead', E_DEPRECATED);
				
			parse_str( file_get_contents('php://input'), $array );
			$_PATCH = $array;
			unset($array);
		}

		try {
			$ApiObj = new RestApi();
			$ApiObj = $ApiObj->getInstance(array(
					'request' => $request_file
			));
				
			// check for "patch" first, fallback to "update"
			if( method_exists($ApiObj, 'patch') ){
				$ApiObj->patch(array(
						'PATCH' => $_PATCH
				));
			} else {
				$ApiObj->update(array(
						'UPDATE' => $_PATCH
				));
			}
				
				
		} catch ( Exception $e ) {
			header('Error', true, $e->getCode() > 0 ? $e->getCode() : 400);
		}

		break;

	case 'PUT':
		$raw_input = file_get_contents('php://input');
		$_PUT = json_decode($raw_input, true);
			
		if( ! $_PUT ){
			parse_str( $raw_input, $array );
			$_PUT = $array;
			unset($array);
		}

		// 		$_PUT = (array)$_PUT;

		try {
			$ApiObj = new RestApi();
			$ApiObj = $ApiObj->getInstance(array(
					'request' => $request_file
			));
			$ApiObj->put(array(
					'PUT' => $_PUT
			));
		} catch ( Exception $e ) {
			print_r($e);exit;
				
			// 			header('Error', true, $e->getCode() > 0 ? $e->getCode() : 400);
		}

		break;

	case 'DELETE':
		parse_str( file_get_contents('php://input'), $array );
		$_DELETE = $array;
		unset( $array );

		$ApiObj = new RestApi();
		$ApiObj = $ApiObj->getInstance(array(
				'request' => $request_file
		));
		$ApiObj->delete(array(
				'DELETE' => $_DELETE
		));

		break;


}



