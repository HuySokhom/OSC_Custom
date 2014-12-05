<?php
class RestApiImageSlider extends RestApi{
	
	public function get($params = array()){
		$image = new oscImageCollection();
		$image->setDbRes(RestApiResources::getDbRes());
		

		///////////////////////
		/// show multi data ///
		///////////////////////
		
		$image_array = array();
		
		$image->populate();
		foreach ($image->getElements() as $element ){
			$element->load();
			$image_array[] = array(
				'id' => $element->getId(),
				'title' => $element->getTitle(),
				'image' => $element->getImage(),
				'link' => $element->getLink()
			);
		}
		
		return array(
			'data' => $image_array
		);
		
		/////////////////////////////
		// show with filter option // 
		/////////////////////////////
		
		$image->setFilter('id' , 1);
		
		if( $image->getTotalCount() > 0 ){
			$image->populate();
			$element = $image->getFirstElement();
		}else {
			throw new Exception(
				"404: image Not Found",
				404
			);
		}
		
		$element->load();
		return array(
			'data' => array(
				'id' => $element->getId(),
				'title' => $element->getTitle(),
				'image' => $element->getImage(),
				'link' => $element->getLink()
			)
		);
		
		///////////////////
		// end of sample //
		///////////////////
	}	
	
	public function post( $params = array() ){
		
		$image = new oscImage();
		$image->setDbRes(RestApiResources::getDbRes());
		
		$image->setImage($params['POST']['image']);
		$image->setTitle($params['POST']['title']);
		$image->setLink($params['POST']['link']);
	
		$image->save();
	
		return array(
			'data' => array (
				'image_id' => $image->getId()
			)
		);
		
	}
	
}