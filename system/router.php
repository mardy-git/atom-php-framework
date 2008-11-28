<?php
/**
 * Atom router implementation
 *
 * This file contains class AtomRouter which in charge to call proper controller from "extensions" folder based on the URL  
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */
 
class AtomRouter {
	private $uri;
	private $method;
	private $input_data;
	private $id;
	
	public function __construct(){
		global $config;
		
		//process uri
		$uri = $_SERVER["REQUEST_URI"];
		// Kill the query string
		$uri = explode("?",$uri);
		$uri = $uri[0];
		//full web root url
		$url = parse_url($config['web_root']);
		if(strpos($uri,$url['path'])==0)
			$uri = substr($uri,strlen($url['path']));
		
		//remove beginning/ending slashes
		if(substr($uri,0,1) == "/") $uri = substr($uri,1);
		if(substr($uri,-1,1) == "/") $uri = substr($uri,0,-1);
		
		$this->uri = $uri;
		//get method
		$this->method=$_SERVER["REQUEST_METHOD"];
		//input data for post (create) and put (update)
		if ($this->method=="POST" || $this->method=="PUT") {
			$this->input_data = file_get_contents("php://input");
		}
		//get id
		$this->id = $_REQUEST['id'];
	}

	public function get_controller_class_name(){
		$ns = explode("/",$this->uri);
		if(count($ns) == 1 && $ns[0] == "")
			return "IndexController";
		else
			return implode("_",$ns) . "Controller";
	}
	
	public function get_controller_file_name(){
		$ns = explode("/",$this->uri);
		if(count($ns) == 1 && $ns[0] == "")
			return EXTENSIONPATH . "index.php";
		else
			return EXTENSIONPATH . $ns[0] . ".php";		
	}
	
	public function dispatch(){
		$file_name = $this->get_controller_file_name();
		$class_name = $this->get_controller_class_name();
		if(is_file($file_name)){			
			include_once($file_name);
			if(class_exists($class_name)){
				$controller = new $class_name;
				if(method_exists($controller,$this->method)){
					return $controller->{$this->method}($this->id,$this->input_data);	
				}
				else{
					AtomController::header_status(STATUS_BAD_REQUEST);
					return false;
				}
			}
			else{
				AtomController::header_status(STATUS_BAD_REQUEST);
				return false;
			}
		}
		else{
			AtomController::header_status(STATUS_BAD_REQUEST);
			return false;
		}
	}
}
	
?>