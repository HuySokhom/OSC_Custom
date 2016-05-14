<?php
class oscImage extends DatabaseLoadableObject {
	
	protected 
		$title,
		$link,
		$image
	;
	
	public function load(){
		if( !$this->getId() ) {
			throw new Exception("load method requires id to be set");
		}
		
		$q = $this->dbQuery("
			SELECT
				title,
				image,
				link
			FROM
				image_slider
			WHERE
				id = '" . (int)$this->getId() . "'
		");
		
		if( $this->dbNumRows( $q ) < 1 ) {
			throw new Exception("image_slider id [" . $this->getId() . "] could not be loaded");
		}
		
		$r = $this->dbFetchArray( $q );
		
		$this->title = $r['title'];
		
		$this->link = $r['link'];
		$this->image = $r['image'];	
	}
	
	public function save(){
		
		if( !$this->getId() ) {
			throw new Exception("insert method requires id to be set");
		}
		
		$this->dbQuery("
			INSERT INTO
				image_slider
			(
				title,
				image,
				link
			)
				VALUES
			(
				'" . $this->dbEscape( $this->getTitle() ) . "',
 				'" . $this->dbEscape( $this->getImage() ) . "',
 				'" . $this->dbEscape( $this->getLink() ) . "'
			)
		");
		
		$this->setId( $this->dbInsertId() );
		
	}
	public function getTitle() {
		return $this->title;
	}
	
	public function getImage() {
		return $this->image;
	}
	
	public function getLink() {
		return $this->link;
	}
	
	public function setTitle( $title ){
		$this->title = $title;
	}
	
	public function setLink( $link ) {
		$this->link = $link;
	}
	
	public function setImage( $image ){
		$this->image = $image;
	}
}