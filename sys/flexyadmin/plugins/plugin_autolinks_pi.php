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
		$this->act_on(array('changedFields'=>'uri,str_tags'));
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
		$this->CI->_add_content(h($this->plugin,1));
		if (!empty($args)) {
			if (isset($args[0])) {
				$action=$args[0];
				array_shift($args);
				switch ($action) {
					case 'resettags':
						$this->_resetTags();
						break;
					case 'render':
						$this->_render($args);
						break;
				}
			}
		}
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
		// if a uri has changed, replace all those uri's
		if ($this->newData['uri']!=$this->oldData['uri']) {
			$this->CI->db->set('uri',$newData['uri']);
			$this->CI->db->where('uri',$oldData['uri']);
			$this->CI->db->update('res_tags');
			$this->_setRender();
		}
		
		// if a tag field has changed, remove old tags, add new tags
		if ($this->newData['str_tags']!=$this->oldData['str_tags']) {
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
					if (!in_array($t,$oldTags)) $addTags[]=$t;
				}
				foreach ($addTags as $key => $value) {
					$this->CI->db->set('uri',$this->newData['uri']);
					$this->CI->db->set('str_tag',$value);
					$this->CI->db->insert('res_tags');
				}
				$this->_setRender();
			}
			// strace_(array('oldTags'=>$oldTags,'newTags'=>$newTags,'changed?'=>($oldTags!=$newTags),'removed'=>$removedTags,'added'=>$addTags));
		}
		return FALSE;
	}

	function _after_delete() {
		// delete all entries of deleted uri from tags
		if (isset($this->oldData['uri'])) {
			$uri=$this->oldData['uri'];
			$this->CI->db->where('uri',$uri);
			$areThere=$this->CI->db->get_results('res_tags');
			if ($areThere) {
				$this->CI->db->where('uri',$uri);
				$this->CI->db->delete('res_tags');
				$this->_setRender();
			}
		}
		return FALSE;
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
		$tags=array();
		foreach ($tables as $table) {
			$this->CI->db->select('uri,str_tags');
			$res=$this->CI->db->get_results($table);
			foreach ($res as $key => $value) {
				if (!empty($this->cfg[$table]))
					$uri=$this->cfg[$table].'/'.$value['uri'];
				else
					$uri=$value['uri'];
				$theseTags=$this->_trimExplode($value['str_tags']);
				foreach ($theseTags as $tag) {
					if (!isset($tags[$tag])) $tags[$tag]=array('uri'=>$uri, 'tag'=>$tag, 'len'=>strlen($tag));
				}
			}
		}
		// order on length of tag
		$f = "return (\$a['len'] < \$b['len']);";
		uasort($tags, create_function('$a,$b', $f));
		// put in db
		foreach ($tags as $key => $value) {
			$this->CI->db->set('uri',$value['uri']);
			$this->CI->db->set('str_tag',$value['tag']);
			$this->CI->db->insert('res_tags');
		}
		$this->CI->_add_content('<p>Tags created</p>');
		
		$this->_setRender();
	}
	
	
	function _render($args=NULL) {
		if (isset($args[0]) and !empty($args[0])) $id=$args[0];
		if (isset($args[1]) and !empty($args[1]))	$table=$args[1]; else	$table='tbl_articles';
		
		$this->CI->load->model("grid");
		$this->CI->lang->load("help");
		$actionGrid=new grid();
		
		// test render
		$this->CI->db->select('id,uri,str_titel,str_tags');
		$this->CI->db->order_by('int_stat','DESC');
		$articles=$this->CI->db->get_results($table,50);
		foreach ($articles as $id => $article) {
			$articles[$id]['uri']='admin/plugin/ajax/autolinks/render/'.$id;
		}
		$actionGrid->set_data($articles,'Render Articles');
		
		$this->_setRender(false);
		$this->CI->_add_content($actionGrid->view('html',$table,'grid actionGrid'));
		$this->CI->_show_type("plugin grid");
	}

	function _ajaxRender($args=NULL) {
		if (isset($args[0]) and !empty($args[0])) $id=$args[0];
		if (isset($args[1]) and !empty($args[1]))	$table=$args[1]; else	$table='tbl_articles';
		if (isset($id)) {$this->CI->db->where('id',$id);}
		$this->CI->db->order_by('int_stat','DESC');
		$article=$this->CI->db->get_row($table);
		if (!empty($article)) {
			$id=$article['id'];
			$txt=$article['txt_tekst'];
			$renderedTxt=$this->_doRender($txt);
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
			$tags=$this->CI->db->get_result('res_tags');
			// put them in search/replace array
			$search=array();
			$replace=array();
			$pattern='/\b(##)\b(?=[^>]*?<)(?!\s*<\s?\s?a\s?>)/'; 
			foreach ($tags as $key => $value) {
				$search[]=str_replace('##',$value['str_tag'],$pattern);
				$replace[]='<a class="autoLink" href="'.site_url().$value['uri'].'">$1</a>';
			}
			return array('tags'=>$tags,'search'=>$search,'replace'=>$replace);
		}
	}

	function _doRender($txt) {
		// make sure txt is is embedded in <p> tags
		if (substr($txt,1,3)!='<p>') $txt=p().$txt._p();
		// get all tags & uris
		$tags=$this->_getTags();
		// render it!
		$txt=preg_replace($tags['search'],$tags['replace'],$txt);
		return $txt;
	}
	
}

?>