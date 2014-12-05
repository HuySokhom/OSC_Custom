<?php
class testclass extends DatabaseLoadableObject{
	
	protected 
		$username
	;
	public function load(){
		$q = $this->dbQuery("
			SELECT
				user_name
			FROM
				administrators
			WHERE
				id = '" . $this->getId() . "'
		");
		
		if( $this->dbNumRows( $q ) < 1 ) {
			throw new Exception(" could not load id [" . $this->getId() . "]");
		}
		echo $q;
		$r = $this->dbFetchArray( $q );
		
		$this->username = $r['user_name'];
		
	}
	
	public function setDbRes( $dbRes ) {print_r( $dbRes);
		parent::setDbRes( $dbRes );
	}
	
	
	public function test(){
		return $this->username;
	}
	
}