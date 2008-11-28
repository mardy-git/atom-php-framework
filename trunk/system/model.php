<?php
/**
 * Atom model abstract
 *
 * This file contains class AtomModel as abstract class. Extend this class save, read, update, delete atom object to your particular
 * storage like database    
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */

class AtomModel {
	public $atom = null;
	public $controller = null;
	
	public function __construct(&$controller){
		$this->controller = &$controller;
	}
	
	public function create(&$id,&$atom,&$status){	
	}
	
	public function retrieve($id,&$atom,&$status){
	}
	
	public function update($id,&$atom,&$status){
		
	}
	
	public function delete($id,&$status){
		
	}
	
}
	
?>