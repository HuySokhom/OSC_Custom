<?php

abstract class DatabaseLoadableObjectPolymorphic extends DatabaseLoadableObject {
	
	abstract public function getObjectType();
	
}