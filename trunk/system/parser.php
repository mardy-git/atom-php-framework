<?php
/**
 * Atom parser implementation
 *
 * This file contains class AtomParser which populates atom entities defined in atom.php
 * AtomParser uses php 5 simplexml library  
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf
 */
 
class AtomParser {
	protected $xml_parser = NULL;
	protected $namespaces = array();
	protected $atom = NULL;
	public $controller = null;

public function __construct(&$controller){
	$this->controller = &$controller;
}

public function parse(&$xml_data) {
	$this->xml_parser = simplexml_load_string($xml_data);
	$this->namespaces = $this->xml_parser->getNamespaces(true);
	$this->register_namespaces($this->xml_parser);
	$feed_array = $this->xml_parser->xpath("/atom:feed");
	if(is_array($feed_array) && count($feed_array))
		$this->populate_feed($this->atom,$this->xml_parser);
	else
		$this->populate_entry($this->atom,$this->xml_parser);
	return $this->atom;
}

public function print_out(&$atom){
	$xml_parser = simplexml_load_string("<?xml version=\"1.0\" encoding=\"" . $atom->encoding . "\"?>" . $atom->get_xml());
	echo $xml_parser->asXML();
}

protected function register_namespaces(&$xml_object){
	reset($this->namespaces);
	while(list($prefix,$ns) = each($this->namespaces)) {
		if($prefix == "") $prefix = "atom";
		$xml_object->registerXPathNamespace($prefix,$ns);
	}
}

function populate_common(&$atom_parent,&$xml_element,$callback){
	if(is_array($xml_element)){
		reset($xml_element);
		while(list(,$curr_element) = each($xml_element)){
			if(is_object($curr_element)){
				$this->$callback($atom_parent,$curr_element);
			}
		}
	}
	elseif(is_object($xml_element)){
		$this->$callback($atom_parent,$xml_element);	
	}	
}


protected function populate_author(&$atom_parent,&$xml_element){
	$author = new AtomPerson("author");
	$this->set_simple_value($author,"","name",$xml_element,"","name");
	$this->set_simple_value($author,"","uri",$xml_element,"","uri");
	$this->set_simple_value($author,"","email",$xml_element,"","email");
	$this->populate_common_attributes($xml_element,$author);
	$atom_parent->authors[] = &$author;
}

protected function populate_category(&$atom_parent,&$xml_element){
	$category = new AtomCategory("category");
	$this->set_simple_value($category,"","term",$xml_element,"term","");
	$this->set_simple_value($category,"","scheme",$xml_element,"scheme","");
	$this->set_simple_value($category,"","label",$xml_element,"label","");
	$this->populate_common_attributes($xml_element,$category);
	$atom_parent->categories[] = &$category;
}

protected function populate_contributor(&$atom_parent,&$xml_element){
	$contributor = new AtomPerson("contributor");
	$this->set_simple_value($contributor,"","name",$xml_element,"","name");
	$this->set_simple_value($contributor,"","uri",$xml_element,"","uri");
	$this->set_simple_value($contributor,"","email",$xml_element,"","email");
	$this->populate_common_attributes($xml_element,$contributor);
	$atom_parent->contributors[] = &$contributor;
}

protected function populate_generator(&$atom_parent,&$xml_element){
	$generator = new AtomGenerator("generator");
	$this->set_simple_value($generator,"","uri",$xml_element,"uri","");
	$this->set_simple_value($generator,"","version",$xml_element,"version","");
	$this->populate_common_attributes($xml_element,$generator);
	$atom_parent->generator = &$generator;
}

protected function populate_icon(&$atom_parent,&$xml_element){
	$icon = new AtomIcon("icon");
	$this->set_simple_value($icon,"","uri",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$icon);
	$atom_parent->icon = &$icon;
}

protected function populate_id(&$atom_parent,&$xml_element){
	$id = new AtomId("id");
	$this->set_simple_value($id,"","uri",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$id);
	$atom_parent->id = &$id;
}

protected function populate_link(&$atom_parent,&$xml_element){
	$link = new AtomLink("link");
	$this->set_simple_value($link,"","href",$xml_element,"href","");
	$this->set_simple_value($link,"","rel",$xml_element,"rel","");
	$this->set_simple_value($link,"","type",$xml_element,"type","");
	$this->set_simple_value($link,"","hreflang",$xml_element,"hreflang","");
	$this->set_simple_value($link,"","title",$xml_element,"title","");
	$this->set_simple_value($link,"","length",$xml_element,"length","");
	$this->populate_common_attributes($xml_element,$link);
	$atom_parent->links[] = &$link;
}

protected function populate_logo(&$atom_parent,&$xml_element){
	$logo = new AtomLogo("logo");
	$this->set_simple_value($logo,"","uri",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$logo);
	$atom_parent->logo = &$logo;
}

protected function populate_rights(&$atom_parent,&$xml_element){
	$rights = new AtomText("rights","");
	$this->set_simple_value($rights,"","type",$xml_element,"type","");
	$this->set_simple_value($rights,"","text",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$rights);
	$atom_parent->rights = &$rights;
}

protected function populate_subtitle(&$atom_parent,&$xml_element){
	$subtitle = new AtomText("subtitle","");
	$this->set_simple_value($subtitle,"","type",$xml_element,"type","");
	$this->set_simple_value($subtitle,"","text",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$subtitle);
	$atom_parent->subtitle = &$subtitle;
}

protected function populate_title(&$atom_parent,&$xml_element){
	$title = new AtomText("title","");
	$this->set_simple_value($title,"","type",$xml_element,"type","");
	$this->set_simple_value($title,"","text",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$title);
	$atom_parent->title = &$title;
}

protected function populate_updated(&$atom_parent,&$xml_element){
	$updated = new AtomDate("updated",0);
	$updated->timestamp = strtotime($xml_element[0]);
	$this->populate_common_attributes($xml_element,$updated);
	$atom_parent->updated = &$updated;
}

protected function populate_content(&$atom_parent,&$xml_element){
	$content = new AtomText("content","");
	$this->set_simple_value($content,"","type",$xml_element,"type","");
  // end-tag is fixed in form so it's easy to replace
  $html = str_replace("</content>","",$xml_element->asXML());
  // remove start-tag, possibly including attributes and white space
  $html = ereg_replace("<content[^>]*>","",$html);
	
	$content->text = $html;
	$this->populate_common_attributes($xml_element,$content);
	$atom_parent->content = &$content;	
}

protected function populate_published(&$atom_parent,&$xml_element){
	$published = new AtomDate("published",0);
	$published->timestamp = strtotime($xml_element[0]);
	$this->populate_common_attributes($xml_element,$published);
	$atom_parent->published = &$published;
}

protected function populate_summary(&$atom_parent,&$xml_element){
	$summary = new AtomText("summary","");
	$this->set_simple_value($summary,"","type",$xml_element,"type","");
	$this->set_simple_value($summary,"","text",$xml_element,0,"");
	$this->populate_common_attributes($xml_element,$summary);
	$atom_parent->summary = &$summary;
}

protected function populate_entry(&$atom_parent,&$xml_element){
	$entry = new AtomEntry("entry");
	
	if(is_object($atom_parent)){
		$local_xml = simplexml_load_string($xml_element->asXML());
		$this->register_namespaces($local_xml);
	}
	else{
		$local_xml = $xml_element;
	}
	
	$this->populate_common($entry,$local_xml->author,"populate_author");
	$this->populate_common($entry,$local_xml->category,"populate_category");
	$this->populate_common($entry,$local_xml->content,"populate_content");
	$this->populate_common($entry,$local_xml->contributor,"populate_contributor");
	$this->populate_common($entry,$local_xml->id,"populate_id");
	$this->populate_common($entry,$local_xml->link,"populate_link");
	$this->populate_common($entry,$local_xml->published,"populate_published");
	$this->populate_common($entry,$local_xml->rights,"populate_rights");
	//source
	$this->populate_common($entry,$local_xml->summary,"populate_summary");
	$this->populate_common($entry,$local_xml->title,"populate_title");
	$this->populate_common($entry,$local_xml->updated,"populate_updated");	
	
	$this->populate_common_attributes($local_xml,$entry);
	
	if(is_object($atom_parent)){
		$atom_parent->entries[] = &$entry;
	}
	else{
		$this->atom = &$entry;
	}
}

protected function populate_feed(&$atom_parent,&$xml_element){
	$feed = new AtomFeed("feed");
	
	$this->populate_common($feed,$xml_element->author,"populate_author");
	$this->populate_common($feed,$xml_element->category,"populate_category");
	$this->populate_common($feed,$xml_element->contributor,"populate_contributor");
	$this->populate_common($feed,$xml_element->generator,"populate_generator");
	$this->populate_common($feed,$xml_element->icon,"populate_icon");
	$this->populate_common($feed,$xml_element->id,"populate_id");
	$this->populate_common($feed,$xml_element->link,"populate_link");
	$this->populate_common($feed,$xml_element->logo,"populate_logo");
	$this->populate_common($feed,$xml_element->rights,"populate_rights");
	$this->populate_common($feed,$xml_element->subtitle,"populate_subtitle");
	$this->populate_common($feed,$xml_element->title,"populate_title");
	$this->populate_common($feed,$xml_element->updated,"populate_updated");
	$this->populate_common($feed,$xml_element->entry,"populate_entry");
	
	$this->populate_common_attributes($xml_element,$feed);
	if(is_object($atom_parent))
		$atom_parent->feeds[] = &$feed;
	else
		$this->atom = &$feed;
}

protected function populate_common_attributes(&$xml_object,&$atom_element){
	$this->set_simple_value($atom_element,"base","",$xml_object,"base","");
	$this->set_simple_value($atom_element,"lang","",$xml_object,"lang","");
}

protected function set_simple_value(&$atom_element,$atom_attribute_name,$atom_property_name,&$xml_object,$xml_attribute_name,$xml_property_name,$inner_content = false){
	//get value from xml
	if($xml_attribute_name != "" || is_numeric($xml_attribute_name)){
		$value = (string)$xml_object[$xml_attribute_name];
	}
	else{
		if($inner_content){
			$value = (string)$xml_object->$xml_property_name->AsXML();
		}
		else{
			$value = (string)$xml_object->$xml_property_name;
		}
	}
	//set value to atom object
	if(isset($value)){
		if($atom_attribute_name){
			$atom_element->attributes[$atom_attribute_name] = $value;
		}
		if($atom_property_name){
			$atom_element->$atom_property_name = $value;
		}
	}
}

}