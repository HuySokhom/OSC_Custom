<?php
class oscImage extends DatabaseLoadableObject {
	
	protected 
		$title,
		$link,
		$image
	;
	
	public function load(){
		// @todo
	}
	
	public function save(){
		
		if( !$this->getId() ) {
			throw new Exception("insert method requires id to be set");
		}
		
		$this->dbQuery("
			INSERT INTO
				specials
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