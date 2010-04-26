<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin template
 *
 * @author Jan den Besten
 */


class plugin_autolinks extends plugin_ {

	var $needToRender;
	var $tagsPlus;

	function init($init=array()) {
		parent::init($init);
		$this->needToRender=$this->CI->session->userdata('render');
		$this->tagsPlus=false;
		$this->act_on(array('existingTables'=>'res_tags','changedFields'=>'uri,str_tags,txt_text'));
	}
	
	//
	// Here you find short templates of possible methods
	//


	//
	// _admin_logout is a call that's made when user is logging out
	//
	function _admin_logout() {
		if ($this->CI->session->userdata('render')) {
			redirect('admin/plugin/autolinks/render');
		}
	}

	
	//
	// _admin_api is a call in admin:
	// admin/plugin/#plugin_name# 
	//
	function _admin_api($args=NULL) {
		$action='';
		if (isset($args[0])) {
			$action=$args[0];
			array_shift($args);
		}
		switch ($action) {
			case 'resettags':
				$this->CI->_add_content(h($this->plugin,1));
				$this->_resetTags();
				break;
			case 'render':
			default:
				$this->_render($args);
				break;
		}
	}
	function _admin_api_calls() {
		return array('resettags','render');
	}


	//
	// _admin_api is a call in admin:
	// admin/plugin/#plugin_name# 
	//
	function _ajax_api($args=NULL) {
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

	function _after_update() {
		$changed=false;
		
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

	function _after_delete() {
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
		return FALSE;
	}
	
	
	function _get_full_uri($table,$id) {
		if ($this->CI->db->field_exists('self_parent',$table)) {
			$this->CI->db->select('id,self_parent');
			$this->CI->db->uri_as_full_uri();
		}
		$this->CI->db->select('uri');
		$result=$this->CI->db->get_result($table);
		return $result[$id]['uri'];
	}
	
	function _setRender($render=true) {
		$this->needToRender=$render;
		$this->CI->session->set_userdata('render',$render);
	}
	
	function _trimExplode($s) {
		$a=explode(',',$s);
		foreach ($a as $k => $v) {
			$a[$k]=trim($v);
		}
		return $a;
	}
	
	function _resetTags() {
		// empty tag table
		$this->CI->db->truncate('res_tags');
		
		// scan all tags and put in array with uri
		$tables=array_keys($this->cfg);
		foreach ($tables as $key=>$table) {
			$pre=get_prefix($table);
			if (!in_array($pre,array('tbl','cfg','log','res','rel'))) unset($tables[$key]);
		}
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
					if (!empty($this->cfg[$table]))
						$uri=$this->cfg[$table].'/'.$value['uri'];
					else
						$uri=$value['uri'];
					$theseTags=$this->_trimExplode($value['str_tags']);
					foreach ($theseTags as $tag) {
						$tag=strtoupper($tag);
						if (!isset($tags[$tag])) $tags[$tag]=array('uri'=>$uri, 'tag'=>$tag, 'len'=>strlen($tag));
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
	
	function _addTags($tags) {
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
	
	
	function _render($args=NULL) {
		if (isset($args[0]) and !empty($args[0]))	$table=$args[0]; else	$table='';
		if (isset($args[1]) and !empty($args[1])) $id=$args[1];

		// TODO, duidelijke keuze tussen een record van een tabel, of een tabel of als niets gegeven alle tabbellen in de plugin config

		if (isset($id) and isset($table)) {
 			// just one record from a table
			if (empty($table)) $table='tbl_articles';
			$this->CI->db->select('id,uri,str_title,str_tags,txt_text');
			$this->CI->db->where('id',$id);
			$articles=$this->CI->db->get_results($table);
			$article=current($articles);
			$txt=$this->_doRender($article['txt_text'],$article['uri']);
			$this->CI->_add_content('<h1>'.$article['str_title'].'</h1>');
			$this->CI->_add_content($txt);
		}
		else {
			// AJAX render
			$this->CI->load->model("grid");
			$this->CI->lang->load("help");
			$actionGrid=new grid();

			if (empty($table)){
				$tables=array_keys($this->cfg);
				foreach ($tables as $key=>$table) {
					$pre=get_prefix($table);
					if (!in_array($pre,array('tbl','cfg','res'))) unset($tables[$key]);
				}
			}
			else
				$tables=array($table);

			$ajaxTable=array();
			foreach ($tables as $table) {
				$this->CI->db->select('id,str_title');
				$this->CI->db->order_by('str_title');
				$articles=$this->CI->db->get_results($table);
				foreach ($articles as $id => $article) {
					$articles[$id]['table']=$table;
					$articles[$id]['uri']='admin/plugin/ajax/autolinks/render/'.$id.'/'.$table;
				}
				$ajaxTable=array_merge($ajaxTable,$articles);
			}
			$actionGrid->set_data($ajaxTable,'Render...');
			$this->CI->_add_content($actionGrid->view('html',$table,'grid actionGrid'));
			$this->CI->_show_type("plugin grid");
			$this->_setRender(false);
		}
		
		
	}

	function _ajaxRender($args=NULL) {
		if (isset($args[0]) and !empty($args[0])) $id=$args[0];
		if (isset($args[1]) and !empty($args[1]))	$table=$args[1]; else	$table='tbl_articles';
		if (isset($id)) {$this->CI->db->where('id',$id);}
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
			$this->CI->db->set('txt_rendered',$renderedTxt);
			$this->CI->db->where('id',$id);
			$this->CI->db->update($table);
		}
		else echo 'ajaxRender Error';
	}

	function _getTags() {
		if ($this->tagsPlus)
			return $this->tagsPlus;
		else {
			$this->CI->db->order_by('int_len','desc');
			$tags=$this->CI->db->get_result('res_tags');
			// put them in search/replace array
			$search=array();
			$replace=array();
			/* $pattern='/\b(##)\b(?=[^>]*?<)(?!\s*<\s?\s?a\s?>)/i';  */
			$pattern='/\b(##)\b(?!([\w\d\s]*?)<\/a\s?>)(?=[^>]*<)/i'; 
			foreach ($tags as $key => $value) {
				$uri=$value['uri'];
				$tag=$value['str_tag'];
				$search[$tag]=str_replace('##',$value['str_tag'],$pattern);
				$replace[$tag]='<a class="autoLink" href="'.$uri.'">$1</a>';
			}
			return array('tags'=>$tags,'search'=>$search,'replace'=>$replace);
		}
	}

	function _doRender($txt,$uri='') {
		// make sure txt is in embedded in <p> tags
		if (substr($txt,1,3)!='<p>') $txt=p().$txt._p();
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
		if (isset($this->cfg['limit'])) $limit=$this->cfg['limit']; else $limit=-1;
		$txt=preg_replace($tags['search'],$tags['replace'],$txt,$limit);
		return $txt;
	}
	
}

?>