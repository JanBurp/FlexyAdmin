<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class Plugin_autolinks extends Plugin {

   var $needToRender;
 	 var $tagsPlus;
   
   public function __construct() {
     parent::__construct();
 		 $this->needToRender=$this->CI->session->userdata('render');
 		 $this->tagsPlus=false;
   }


	public function _admin_logout() {
		if ($this->CI->session->userdata('render')) {
			redirect('admin/plugin/autolinks/render');
		}
	}

	
	public function _admin_api($args=NULL) {
		$action='';
		if (isset($args[0])) {
			$action=$args[0];
			array_shift($args);
		}
		switch ($action) {
			case 'resettags':
				$this->CI->_add_content(h($this->name,1));
				$this->_resetTags();
				break;
			case 'render':
        $this->_resetTags();
			  $this->_render($args);
        break;
      default:
        array_unshift($args,$action);
        $this->_resetTags();
				$this->_render($args);
				break;
		}
	}
	private function _admin_api_calls() {
		return array('resettags','render');
	}


	//
	// _admin_api is a call in admin:
	// admin/plugin/#plugin_name# 
	//
	public function _ajax_api($args=NULL) {
		if (!empty($args)) {
			if (isset($args[0])) {
				$action=$args[0];
				array_shift($args);
				switch ($action) {
					case 'render':
						$this->_ajaxRender($args);
						break;
				}
			}
		}
	}


	// These methods can be used to do some actions 

	public function _after_update() {
		$changed=$this->newData;
		
		// if a uri has changed, replace all those uri's
		if (isset($this->newData['uri']) and $this->newData['uri']!=$this->oldData['uri']) {
			if (isset($this->newData['self_parent']) and ($this->newData['self_parent']!=0) ) {
				$parentUri=$this->_get_full_uri($this->table,$this->newData['self_parent']);
				$uri=$parentUri.'/'.$this->newData['uri'];
				$oldUri=$parentUri.'/'.$this->oldData['uri'];
			}
			else {
				$uri=$this->newData['uri'];
				$oldUri=$this->oldData['uri'];
			}
			$this->CI->db->set('uri',$uri);
			$this->CI->db->where('uri',$oldUri);
			$this->CI->db->update('res_tags');
			$this->_setRender();
		}
		
		// if a tag field has changed, remove old tags, add new tags
		if (isset($this->newData['str_tags']) and $this->newData['str_tags']!=$this->oldData['str_tags']) {
			$oldTags=$this->_trimExplode($this->oldData['str_tags']);
			$newTags=$this->_trimExplode($this->newData['str_tags']);
			// check if it is realy different
			if ($oldTags!=$newTags) {
				// removed tags
				$removedTags=array();
				foreach ($oldTags as $t) {
					if (!in_array($t,$newTags)) $removedTags[]=$t;
				}
				foreach ($removedTags as $key => $value) {
					$this->CI->db->where('str_tag',$value);
					$this->CI->db->delete('res_tags');
				}
				// added tags
				$addTags=array();
				foreach ($newTags as $t) {
					$t=strtoupper($t);
					if (!in_array($t,$oldTags)) $addTags[]=$t;
				}
				$this->_addTags($addTags);
				$this->_setRender();
			}
			// strace_(array('oldTags'=>$oldTags,'newTags'=>$newTags,'changed?'=>($oldTags!=$newTags),'removed'=>$removedTags,'added'=>$addTags));
		}
		
		// if a txt field has changed, render it
		if (isset($this->newData['txt_text']) and $this->newData['txt_text']!=$this->oldData['txt_text']) {
			$txt=$this->newData['txt_text'];
			$rendered=$this->_doRender($txt,$this->newData['uri']);
			$this->newData['txt_rendered']=$rendered;
			$changed=$this->newData;
		}
		return $changed;
	}

	public function _after_delete() {
		// delete all entries of deleted uri from tags
		if (isset($this->oldData['uri'])) {
			$uri=$this->oldData['uri'];
			$sql="SELECT id FROM `res_tags` WHERE `uri`='$uri' OR `uri` LIKE '%/$uri'";
			$query=$this->CI->db->query($sql);
			$areThere=$query->result_array(); //$this->CI->db->get_results('res_tags');
			$lastq=$this->CI->db->last_query();
			if ($areThere) {
				foreach ($areThere as $row) {
					$this->CI->db->or_where('id',$row['id']);
				}
				$this->CI->db->delete('res_tags');
				$this->_setRender();
			}
		}
		return TRUE;
	}
	
	
	private function _get_full_uri($table,$id) {
		if ($this->CI->db->field_exists('self_parent',$table)) {
			$this->CI->db->select('id,self_parent');
			$this->CI->db->uri_as_full_uri();
		}
		$this->CI->db->select('uri');
		$result=$this->CI->db->get_result($table);
		return $result[$id]['uri'];
	}
	
	private function _setRender($render=true) {
		$this->needToRender=$render;
		$this->CI->session->set_userdata('render',$render);
	}
	
	private function _trimExplode($s) {
		$a=explode(',',$s);
		foreach ($a as $k => $v) {
			$a[$k]=trim($v);
		}
		return $a;
	}
	
	private function _resetTags() {
		// empty tag table
		$this->CI->db->truncate('res_tags');
		
		// scan all tags and put in array with uri
		$tables=$this->config['trigger']['tables'];
		$tags=array();
		foreach ($tables as $table) {
			if ($this->CI->db->field_exists('uri',$table) and $this->CI->db->field_exists('str_tags',$table)) {
				$this->CI->db->select('uri,str_tags');
				if ($this->CI->db->field_exists('self_parent',$table)) {
					$this->CI->db->select('id,self_parent');
					$this->CI->db->uri_as_full_uri();
				}
				$res=$this->CI->db->get_results($table);
				foreach ($res as $key => $value) {
					if (!empty($this->config[$table]))
						$uri=$this->config[$table].'/'.$value['uri'];
					else
						$uri=$value['uri'];
					$theseTags=$this->_trimExplode($value['str_tags']);
					foreach ($theseTags as $tag) {
						$tag=strtoupper(trim($tag));
						if (!empty($tag) and !isset($tags[$tag])) {
						  $tags[$tag]=array('uri'=>$uri, 'tag'=>$tag, 'len'=>strlen($tag));
              $etag=htmlentities($tag);
              // tag met htmlentities...
              if ($etag!=$tag) {
                $tags[$etag]=array('uri'=>$uri, 'tag'=>$etag, 'len'=>strlen($tag));
              }
						}
					}
				}
			}
		}
		// put in db
		foreach ($tags as $key => $value) {
			$this->CI->db->set('uri',$value['uri']);
			$this->CI->db->set('str_tag',$value['tag']);
			$this->CI->db->set('int_len',strlen($value['tag']));
			$this->CI->db->insert('res_tags');
		}
		$this->CI->_add_content('<p>Tags created</p>');
		
		$this->_setRender();
	}
	
	private function _addTags($tags) {
		$uri=$this->newData['uri'];
		if (isset($this->newData['self_parent']) and $this->newData['self_parent']!=0) {
			$uri=$this->_get_full_uri($this->table,$this->newData['self_parent']).'/'.$uri;
		}
		foreach ($tags as $key => $value) {
			$this->CI->db->where('uri',$uri);
			$this->CI->db->where('str_tag',$value);
			$this->CI->db->select('id');
			$exist=$this->CI->db->get_row('res_tags');
			if (!$exist) {
				$this->CI->db->set('uri',$uri);
				$this->CI->db->set('str_tag',$value);
				$this->CI->db->set('int_len',strlen($value));
				$this->CI->db->insert('res_tags');
			}
		}
	}
	
	
	public function _render($args=NULL) {
		if (isset($args[0]) and !empty($args[0]))	$table=$args[0]; else	$table='';
		if (isset($args[1]) and !empty($args[1])) $id=$args[1];

		if (isset($id) and isset($table)) {
 			// just one record from a table
			if (empty($table)) $table='tbl_articles';
      if ($this->CI->db->has_field($table,'txt_text')) {
  			$this->CI->db->select('id,uri,str_title,str_tags,txt_text');
  			$this->CI->db->where('id',$id);
  			$articles=$this->CI->db->get_results($table);
  			$article=current($articles);
  			$txt=$this->_doRender($article['txt_text'],$article['uri']);
        $this->_putRenderedDB($table,$id,$txt);
  			$this->CI->_add_content('<h1>'.$article['str_title'].'</h1>');
  			$this->CI->_add_content($txt);
      }
		}
		else {
			// AJAX render
			$this->CI->load->model("grid");
			$this->CI->lang->load("help");
			$actionGrid=new grid();

			$tables=$this->config['trigger']['tables'];

			$ajaxTable=array();
			foreach ($tables as $table) {
        if ($this->CI->db->has_field($table,'txt_text')) {
          $this->CI->db->select('id,str_title');
			    $this->CI->db->order_by('str_title');
          $articles=$this->CI->db->get_results($table);
        
  				foreach ($articles as $id => $article) {
  					$articles[$id]['table']=$table;
  					$articles[$id]['uri']='admin/plugin/ajax/autolinks/render/'.$id.'/'.$table;
  				}
  				$ajaxTable=array_merge($ajaxTable,$articles);
        }
			}
      
			$actionGrid->set_data($ajaxTable,'Render...');
			$this->CI->_add_content($actionGrid->view('html',$table,'grid actionGrid'));
			$this->CI->_show_type("plugin grid");
			$this->_setRender(false);
		}
		
		
	}

	private function _ajaxRender($args=NULL) {
		if (isset($args[0]) and !empty($args[0])) $id=$args[0];
		if (isset($args[1]) and !empty($args[1]))	$table=$args[1]; else	$table='tbl_items';
		if (isset($id)) $this->CI->db->where('id',$id);
		$this->CI->db->order_by('str_title');
		$article=$this->CI->db->get_row($table);
		if (!empty($article)) {
			$id=$article['id'];
			$txt=$article['txt_text'];
			if (isset($article['uri']))
				$uri=$article['uri'];
			else
				$uri='';
			$renderedTxt=$this->_doRender($txt,$uri);
      $this->_putRenderedDB($table,$id,$renderedTxt);
		}
		else echo 'ajaxRender Error';
	}

	private function _getTags() {
    if ($this->tagsPlus)
      return $this->tagsPlus;
    else {
			$this->CI->db->order_by('int_len','desc');
      // $this->CI->db->where('int_len >4');
			$tags=$this->CI->db->get_result('res_tags');
			// put them in search/replace array
			$search=array();
			$replace=array();
			/* $pattern='/\b(##)\b(?=[^>]*?<)(?!\s*<\s?\s?a\s?>)/i';  
			$pattern='/\b(##)\b(?!([\w\d\s]*?)<\/a\s?>)(?=[^>]*<)/i'; */
      
      // Regex:
      // - binnen wordboundaries, maar geen '".- er voor
      // - en niet binnen een a of h tag (erna)
      // - case insensitive over meerdere regels
      $pattern='/([^\"|^\'|^\.|^-])\b(##)\b(?![^>]*<\/[a|h])/uism';
			foreach ($tags as $key => $value) {
				$uri=$value['uri'];
				$tag=$value['str_tag'];
				$search[$tag]=str_replace('##',$value['str_tag'],$pattern);
				$replace[$tag]='$1<a class="autoLink" tag="'.clean_string($value['str_tag']).'" href="'.$uri.'">$2</a>$3$4';
			}
			return array('tags'=>$tags,'search'=>$search,'replace'=>$replace);
    }
	}

	private function _doRender($txt,$uri='') {
		// make sure txt is in embedded in <p> tags
		if (substr($txt,0,3)!='<p>') $txt=p().$txt._p();
		// get all tags & uris
		$tags=$this->_getTags();
    // trace_($tags);
		// remove own tags/uri
		if (!empty($uri)) {
			$removeTags=find_row_by_value($tags['tags'],$uri);
			if ($removeTags) {
				foreach ($removeTags as $id => $value) {
					$tag=$value['str_tag'];
					unset($tags['search'][$tag]);
					unset($tags['replace'][$tag]);
				}
			}
		}
		// render it!
		$txt=preg_replace($tags['search'],$tags['replace'],$txt,$this->config('limit'));
		return $txt;
	}
  
  private function _putRenderedDB($table,$id,$txt) {
		$this->CI->db->set('txt_rendered',$txt);
		$this->CI->db->where('id',$id);
		$this->CI->db->update($table);
  }
	
}

?>