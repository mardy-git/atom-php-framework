<?php
/**
 * Atom controller implementation
 *
 * This file contains class AtomController which in charge for REST/AtomPub protocol implementation  
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */
 
define("STATUS_OK",200);
define("STATUS_CREATED",201);
define("STATUS_NOT_MODIFIED",204);
define("STATUS_BAD_REQUEST",400);
define("STATUS_UNAUTHORIZED",401);
define("STATUS_FORBIDDEN",403);
define("STATUS_NOT_FOUND",404);
define("STATUS_CONFLICT",409);
define("STATUS_INTERNAL_SERVER_ERROR",500);	

class AtomController {
	public $model = null;
	public $atom = null;
	public $parser = null;
	public $status = STATUS_OK;
	
	public function __construct(){
	}
	
	public function post($id,&$data){
		if(is_a($this->model,"AtomModel") && is_a($this->atom,"AtomElement") && is_a($this->parser,"AtomParser")){
			$this->atom = $this->parser->parse($data);			
			if($this->model->create($id,$this->atom,$status)){
				$this->status = STATUS_CREATED;
				$this->print_header();
				$this->model->retrieve($id,$this->atom,$status);
				$this->parser->print_out($this->atom);					
			}
			else{
				$this->status = STATUS_BAD_REQUEST;
				$this->print_header();
			}
		}
		else{
			$this->status = STATUS_INTERNAL_SERVER_ERROR;
			$this->print_header();
		}
	}
	
	public function put($id,&$data){
		if(is_a($this->model,"AtomModel") && is_a($this->atom,"AtomElement") && is_a($this->parser,"AtomParser")){
			$this->atom = $this->parser->parse($data);
			if($this->model->update($id,$this->atom,$status)){
				$this->set_status($status,STATUS_OK);
				$this->print_header();
				$this->model->retrieve($id,$this->atom,$status);
				$this->parser->print_out($this->atom);
			}
			else{
				$this->set_status($status,STATUS_BAD_REQUEST);
				$this->print_header();
			}
		}
		else{
			$this->set_status($status,STATUS_INTERNAL_SERVER_ERROR);
			$this->print_header();
		}
	}
	
	public function get($id,&$data){
		if(is_a($this->model,"AtomModel") && is_a($this->atom,"AtomElement") && is_a($this->parser,"AtomParser")){
			if($this->model->retrieve($id,$this->atom,$status)){
				$this->set_status($status,STATUS_OK);
				$this->print_header();
				$this->parser->print_out($this->atom);
			}
			else{
				$this->set_status($status,STATUS_NOT_FOUND);
				$this->print_header();
			}
		}
		else{
			$this->set_status($status,STATUS_INTERNAL_SERVER_ERROR);
			$this->print_header();
		}
	}
	
	public function delete($id,&$data){
		if(is_a($this->model,"AtomModel")){
			if($this->model->delete($id,$status)){
				$this->set_status($status,STATUS_OK);
				$this->print_header();
			}
			else{
				$this->set_status($status,STATUS_NOT_FOUND);
				$this->print_header();
			}
		}
		else{
			$this->set_status($status,STATUS_INTERNAL_SERVER_ERROR);
			$this->print_header();
		}
	}
	
	public function print_header(){
		$this->header_status($this->status);
		header ("Content-Type: application/atom+xml");
	}
	
	private function set_status($status,$default_status){
		if($status)
			$this->status = $status;
		else
			$this->status = $default_status;
	}
	
	public static function header_status($code){
		$statuses = array(
			200	=> "OK",
			201 => "CREATED",
			304	=> "NOT MODIFIED",
			400	=> "BAD REQUEST",
			401	=> "UNAUTHORIZED",
			403	=> "FORBIDDEN",
			404	=> "NOT FOUND",
			409 => "CONFLICT",
			500	=> "INTERNAL SERVER ERROR"						
		);
		
		header("HTTP/1.0 " . $code . " " . $statuses[$code],true,$code);
	}

}
	
?>