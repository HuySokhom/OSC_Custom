<?php
class RestApiImageSlider extends RestApi{
	
	public function get($params = array()){
		return array(
			'data'=> 'test'			
		);
	}	
	
	public function post( $params = array() ){
		
		return var_dump($params);
		
// 		return ;
		
		
	}
	
}