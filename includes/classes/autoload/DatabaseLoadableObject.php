<?php

/**
 * database loadable object
 * - useful starting point for objects that are loadable from database
 * @author Evan
 *
 */
abstract class DatabaseLoadableObject extends DatabaseObject {
	
	protected
		$id,
		$created,
		$modified,
		$status
	;
	
	abstract public function load();
	
	public function getId() {
		return $this->id;
	}
	
	public function setId( $id ) {
		
		//echo "id is $id <br />";
		$id = (int)$id;
		
		if( $id > 0 ) {
			$this->id = $id;
			return;
		}
		
		throw new Exception(" id must be a positive integer value ");
		
	}
	
	public function getCreated() {
		return $this->created;
	}
	
	public function getModified() {
		return $this->modified;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function setStatus( $id ) {
		$this->status = (int)$id;
	}
	
}