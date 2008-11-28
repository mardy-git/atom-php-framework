<?php
/**
 * Atom entities implementation
 *
 * This files contains classes which abstract atom entities - feed, entries etc. 
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */
class AtomElement {
	public $attributes = array("base"=>"","lang"=>"");
	public $tag = "";
	public $encoding = "UTF-8";
	
	public function __construct($tag = ""){
		if($tag) $this->tag = $tag;
	}
	
	public function get_xml(){
		$content = $this->get_xml_content();
		if($content == ""){
			return "<". $this->tag . $this->get_xml_attribute() . "/>";	
		}
		else
			return "<". $this->tag . $this->get_xml_attribute() . ">" . $content . "</" . $this->tag . ">";
	}
	
	public function get_xml_attribute(){
		$this->update_attributes();
		$result = "";
		reset($this->attributes);
		while(list($name,$value) = each($this->attributes)){
			if(trim($value) != "")
				$result .= " $name=\"" . htmlspecialchars($value) . "\"";
		}
		return $result;
	}
	
	function update_attributes(){
		//TODO
	}
	
	public function get_xml_content(){
		//TODO
	}
	
	public function get_tag_value($tag,$value){
		if(trim($value) == "")
			return "";
		else
			return "<$tag>". htmlspecialchars($value) . "</$tag>";
	}
	
	public static function get_object_xml($object){
		if(is_object($object) && is_a($object,"AtomElement")){
			return $object->get_xml();
		}
		else
			return "";
	}
	
	public static function get_array_xml($array){
		$result = "";
		if(is_array($array)){
			reset($array);
			while(list(,$object) = each($array)){
				$result .= AtomElement::get_object_xml($object);
			}
			return $result;
		}
		else
			return $result;
	}
	
	public static function object_valid($object,$required,&$errors,$element_name){
		if(is_object($object) && is_a($object,"AtomElement")){
			return $object->valid($errors);
		}
		else{
			if($required) $errors[] = "[$element_name] not specified";
			return !$required;
		}
	}
	
	public static function array_valid($array,$required,&$errors,$element_name){
		$result = true;
		if(is_array($array)){
			if(count($array)){
				reset($array);
				while(list(,$object) = each($array)){
					$result =$result && AtomElement::object_valid($object,$required,$errors,$element_name);
				}
				return $result;
			}
			else{
				if($required) $errors[] = "[$element_name(s)] not specified";
				return !$required;
			}
		}
		else{
			if($required) $errors[] = "[$element_name(s)] not specified";
			return !$required;
		}
	}
	
	public function valid(&$errors){
		return true;
	}
	
}

class AtomUri extends AtomElement {
	public $uri = "";
	
	public function __construct($tag = "", $uri = ""){
		parent::__construct($tag);
		$this->uri = $uri;
	}
	
	public function get_xml_content(){
		return htmlspecialchars($this->uri);
	}
	
	public function valid(&$errors){
		if($this->uri == ""){
			$errors[] = "[uri] not specified for [$tag] element";
			return false;
		}
		else
			return true;
	}	
	
}

class AtomPerson extends AtomUri {
	public $name = "";
	public $email = "";
	
	public function __construct($tag,$name = "", $email = "", $uri = ""){
		parent::__construct($tag,$uri);
		$this->name = $name;
		$this->email = $email;
	}
	
	public function get_xml_content(){
		return	$this->get_tag_value("name",$this->name) . 
						$this->get_tag_value("uri",$this->uri) .
						$this->get_tag_value("email",$this->email); 
	}
	
	public function valid(&$errors){
		if($this->name == ""){
			$errors[] = "[name] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}
	}
}

class AtomCategory extends AtomElement {
	public $tag = "category";
	public $term = "";
	public $scheme = "";
	public $label = "";
	
	public function get_xml_content(){
	}
	
	public function __construct($term, $scheme = "", $label = ""){
		parent::__construct($tag,$uri);
		$this->term = $term;
		$this->scheme = $scheme;
		$this->label = $label;
	}
	
	public function update_attributes(){
		parent::update_attributes();
		$this->attributes['term'] = $this->term;
		$this->attributes['scheme'] = $this->scheme;
		$this->attributes['label'] = $this->label;
	}
	
	public function valid(&$errors){
		if($this->term == ""){
			$errors[] = "[term] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}
	}
}

class AtomGenerator extends AtomUri {
	public $tag = "generator";
	public $version = "";
	public $text = "";
	
	public function get_xml_content(){
		return htmlspecialchars($this->text);
	}
	
	public function __construct($text, $version = ""){
		parent::__construct($tag,$uri);
		$this->text = $text;
		$this->version = $version;
	}
	
	public function update_attributes(){
		parent::update_attributes();
		$this->attributes['version'] = $this->version;
		$this->attributes['uri'] = $this->uri;
	}
	
	public function valid(&$errors){
		if($this->text == ""){
			$errors[] = "[text] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}		
	}
}

class AtomIcon extends AtomUri {
	public $tag = "icon";
}

class AtomId extends AtomUri {
	public $tag = "id";
}

class AtomLink extends AtomElement {
	public $tag = "link";
	public $href = "";
	public $rel = "";
	public $type = "";
	public $hreflang = "";
	public $title = "";
	public $length = "";
	
	public function get_xml_content(){
	}
	
	public function __construct($href,$rel = "",$type = "",$hreflang = "",$title = "",$length = ""){
		parent::__construct($tag);
		$this->href = $href;
		$this->rel = $rel;
		$this->type = $type;
		$this->hreflang = $hreflang;
		$this->title = $title;
		$this->length = $length;
	}
	
	public function update_attributes(){
		parent::update_attributes();
		$this->attributes['href'] = $this->href;
		$this->attributes['rel'] = $this->rel;
		$this->attributes['type'] = $this->type;
		$this->attributes['hreflang'] = $this->hreflang;
		$this->attributes['title'] = $this->title;
		$this->attributes['length'] = $this->length;
	}
	
	public function valid(&$errors){
		if($this->href == ""){
			$errors[] = "[href] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}	
	}
}

class AtomLogo extends AtomUri {
	public $tag = "logo";
}

class AtomDate extends AtomElement {
	public $timestamp = "";
	
	public function get_xml_content(){
		return htmlspecialchars(date("c",$this->timestamp));
	}
	
	public function __construct($tag,$timestamp){
		parent::__construct($tag);
		$this->timestamp = $timestamp;
	}
	
	public function valid(&$errors){
		if($this->timestamp == ""){
			$errors[] = "[timestamp] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}
	}
}

class AtomText extends AtomElement {
	public $type = "";
	public $text = "";
	
	public function get_xml_content(){
		if($this->type == "xhtml")
			return $this->text;
		else
			return htmlspecialchars($this->text);
	}
	
	public function __construct($tag,$text,$type = "text"){
		parent::__construct($tag);
		$this->text = $text;
		$this->type = $type;
	}
	
	public function update_attributes(){
		parent::update_attributes();
		$this->attributes['type'] = $this->type;
	}
	
	public function valid(&$errors){
		if($this->text == ""){
			$errors[] = "[text] not specified for [$tag] element";
			return false;	
		}
		else{
			return true;
		}
	}
}

class AtomEntry extends AtomElement {
	public $tag = "entry";
	public $authors = array();
	public $categories = array();
	public $content = NULL;
	public $contributors = array();
	public $id = NULL;
	public $links = array();
	public $published = NULL;
	public $rights = NULL;
	public $source = NULL;
	public $summary = NULL;
	public $title = NULL;
	public $updated = NULL;
	
	public function __construct($includeNS = false){
		parent::__construct("entry");
		if($includeNS)
			$this->attributes["xmlns"] = "http://www.w3.org/2005/Atom";
	}
	
	public function get_xml_content(){
		$result = "";
		//authors
		$result .= $this->get_array_xml($this->authors);
		//categories
		$result .= $this->get_array_xml($this->categories);
		$result .= $this->get_object_xml($this->content);
		//contributors
		$result .= $this->get_array_xml($this->contributors);
		$result .= $this->get_object_xml($this->id);
		//links
		$result .= $this->get_array_xml($this->links);
		
		$result .= $this->get_object_xml($this->published);
		$result .= $this->get_object_xml($this->rights);
		$result .= $this->get_object_xml($this->source);
		$result .= $this->get_object_xml($this->summary);
		$result .= $this->get_object_xml($this->title);
		$result .= $this->get_object_xml($this->updated);
		
		return $result;
	}
	
	public function valid(&$errors){
		$result = true;
		$result = $this->array_valid($this->authors,true,$errors,"author");
		$result = $this->array_valid($this->categories,true,$errors,"category") && $result;
		$result = $this->object_valid($this->content,false,$errors,"content") && $result;
		$result = $this->array_valid($this->contributors,false,$errors,"contributor") && $result;
		$result = $this->object_valid($this->id,true,$errors,"id") && $result;
		$result = $this->array_valid($this->links,false,$errors,"link") && $result;
		$result = $this->object_valid($this->published,false,$errors,"published") && $result;
		$result = $this->object_valid($this->rights,false,$errors,"rights") && $result;
		$result = $this->object_valid($this->source,false,$errors,"source") && $result;
		$result = $this->object_valid($this->summary,false,$errors,"summary") && $result;
		$result = $this->object_valid($this->title,true,$errors,"title") && $result;
		$result = $this->object_valid($this->updated,true,$errors,"updated") && $result;
		
		return $result;
	}		
}


class AtomFeed extends AtomElement {
	public $tag = "feed";
	public $authors = array();
	public $categories = array();
	public $contributors = array();
	public $generator = NULL;
	public $icon = NULL;
	public $id = NULL;
	public $links = array();
	public $logo = NULL;
	public $rights = NULL;
	public $subtitle = NULL;
	public $title = NULL;
	public $updated = NULL;
	public $entries = array();
	
	public function __construct(){
		parent::__construct("feed");
		//default namespace is important
		$this->attributes["xmlns"] = "http://www.w3.org/2005/Atom";
	}
	
	public function get_xml_content(){
		$result = "";
		//authors
		$result .= $this->get_array_xml($this->authors);
		//categories
		$result .= $this->get_array_xml($this->categories);
		//contributors
		$result .= $this->get_array_xml($this->contributors);
		$result .= $this->get_object_xml($this->generator);
		$result .= $this->get_object_xml($this->icon);
		$result .= $this->get_object_xml($this->id);		
		//links
		$result .= $this->get_array_xml($this->links);
		
		$result .= $this->get_object_xml($this->logo);
		$result .= $this->get_object_xml($this->rights);
		$result .= $this->get_object_xml($this->subtitle);
		$result .= $this->get_object_xml($this->title);
		$result .= $this->get_object_xml($this->updated);
		//entries
		$result .= $this->get_array_xml($this->entries);
		
		return $result;
	}
	
	public function valid(&$errors){
		$result = true;
		$result = $this->array_valid($this->authors,true,$errors,"author") && $result;
		$result = $this->array_valid($this->categories,false,$errors,"category") && $result;
		$result = $this->array_valid($this->contributors,false,$errors,"contributor") && $result;
		$result = $this->object_valid($this->generator,false,$errors,"generator") && $result;
		$result = $this->object_valid($this->icon,false,$errors,"icon") && $result;
		$result = $this->object_valid($this->id,true,$errors,"id") && $result;
		$result = $this->array_valid($this->links,false,$errors,"link") && $result;
		$result = $this->object_valid($this->logo,false,$errors,"logo") && $result;
		$result = $this->object_valid($this->rights,false,$errors,"rights") && $result;
		$result = $this->object_valid($this->subtitle,false,$errors,"subtitle") && $result;
		$result = $this->object_valid($this->title,true,$errors,"title") && $result;
		$result = $this->object_valid($this->updated,true,$errors,"updated") && $result;
		$result = $this->array_valid($this->entries,true,$errors,"entry") && $result;
		
		return $result;
	}
}

?>