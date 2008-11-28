<?php
/**
 * Sample Wordpress apf extenesion
 * 
 * @author Milan Rukvina <rukavinamilan@gmail.com>
 * @version 0.1
 * @package apf  
 */
 
//at least you have to extend AtomController 
//have to be named [php_file_name]Controller - ex. if you have xml feed on [apf_root]/wordpress/ file has to be named wordpress.php and class have to be named WordpressController
//further if you have feed for example on [apf_root]/wordpress/category/ file is still wordpress but controller is Wordpress_CategoryController 
class WordpressController extends AtomController{	
	public function __construct(){
		parent::__construct();
		//specify parser, model and atom object
		$this->parser = new AtomParser($this);
		//this one is important - we're using custom model we defined bellow
		//basically we could use custom (extended) parser and atom object as well
		$this->model = new WordpressModel($this);
		//atom object is feed by default
		$this->atom = new AtomFeed;
	}
}

//model class in charge for DB manupulation -> CRUD implementation
class WordpressModel extends AtomModel{
	//specify your wp db info
	private $wp_host	= "localhost";
	private $wp_db		= "";
	private $wp_user	= "";
	private $wp_pass	= "";
	private $wp_pre		= "wp_";
	
	public function __construct(&$controller){
		parent::__construct($controller);
	  mysql_connect($this->wp_host,$this->wp_user,$this->wp_pass);
	  mysql_select_db($this->wp_db);
	}
	
	public function create(&$id,&$atom,&$status){
		//TODO - implement create
	}
	
	public function retrieve($id,&$atom,&$status){
		//if not id specified we're loading full feed
		if(!$id){
			$where = "";
			//load options
			$options = array();
		  $result = mysql_query("SELECT * FROM {$this->wp_pre}options");
		  if($result && mysql_num_rows($result)){
		  	while($l = mysql_fetch_assoc($result)){
					$options[$l['option_name']] = $l['option_value'];
				}
			}
			$atom->title = new AtomText("title",$options['blogname']);
			$atom->id = new AtomId("",$options['siteurl']);
			//get updated
			$result = mysql_query("SELECT UNIX_TIMESTAMP(MAX(post_date)) AS updated FROM {$this->wp_pre}posts");
			if($result && mysql_num_rows($result))
				$atom->updated=new AtomDate("updated",mysql_result($result,0,"updated"));
			//get authors
			$result = mysql_query("SELECT * FROM {$this->wp_pre}users");
			if($result && mysql_num_rows($result)){
				while($l = mysql_fetch_assoc($result)){
					$atom->authors[] = new AtomPerson("author",$l['user_nicename'],$l['user_email '],$l['user_url']);		
				}
			}
			//get categories
			$result = mysql_query("	SELECT t.name
															FROM {$this->wp_pre}terms AS t
																LEFT JOIN {$this->wp_pre}term_taxonomy AS tt ON tt.term_id = t.term_id
															WHERE tt.taxonomy = 'category'");
			if($result && mysql_num_rows($result)){
				while($l = mysql_fetch_assoc($result)){
					$atom->categories[] = new AtomCategory($l['name']);		
				}
			}
		}
		else{
			//if id sepcified we're loading single entry
			$where = "WHERE p.ID = '" . addslashes($id) . "'";
		}
		//read post(s)
    $result = mysql_query("SELECT p.*,UNIX_TIMESTAMP(p.post_modified) AS upost_modified,
															u.*,t.name AS category_name
                           FROM {$this->wp_pre}posts AS p
                             LEFT JOIN {$this->wp_pre}users AS u ON u.ID = p.post_author
                             LEFT JOIN {$this->wp_pre}terms AS t ON t.term_id = p.post_category
                           $where
                           ORDER BY p.post_date DESC
                           LIMIT 0,50");
    if($result && mysql_num_rows($result)){
      while($l = mysql_fetch_array($result)){
				$atom_entry = new AtomEntry($id);
				$atom_entry->title = new AtomText("title",$l['post_title']);
				$atom_entry->id = new AtomId("",$l['guid']);
				$atom_entry->authors[] = new AtomPerson("author",$l['user_nicename'],$l['user_email '],$l['user_url']);
				$atom_entry->categories[] = new AtomCategory($l['category_name']);
				$atom_entry->updated=new AtomDate("updated",$l['upost_modified']);
				$atom_entry->content = new AtomText("content",$l['post_content'],"html");
				if($id){
					//atom object is this entry
					$atom = $atom_entry;
				}
				else
					$atom->entries[] = $atom_entry;
      }
    }
    return true;
	}
	
	public function update($id,&$atom,&$status){
		//TODO - implement update
	}	
}

?>