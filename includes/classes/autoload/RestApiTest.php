<?php
class RestApiTest extends RestApi{	
	public function get($params = array()){
		return array(
			'data'=> 'test'			
		);
	}	
}