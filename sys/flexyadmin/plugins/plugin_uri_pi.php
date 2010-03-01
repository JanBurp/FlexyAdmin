<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."plugins/plugin_.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_uri extends plugin_ {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('fields'=>'uri'));
	}

	function _admin_api($args=NULL) {
		$this->CI->_add_content(h($this->plugin,1));
		if (isset($args)) {
			if (isset($args[0])) {
				$this->table=$args[0];
				if ($this->CI->db->table_exists($this->table) and $this->CI->db->field_exists('uri',$this->table)) {
					// reset all uris of this table
					$allData=$this->CI->db->get_results($this->table);
					foreach ($allData as $id => $data) {
						$this->id=$id;
						$this->oldData=$data;
						$this->newData=$data;
						if (!isset($field)) $field=$this->_get_uri_field();
						$uri=$data['uri'];
						$newUri=$this->_create_uri($field);
						if ($uri!=$newUri) {
									$this->CI->db->set('uri',$newUri);
									$this->CI->db->where('id',$id);
									$this->CI->db->update($this->table);
								}
					}
					$this->CI->_add_content("<p>All uri's in $this->table are (re)set.</p><p>Just change one in this table to make sure all other plugins did there work.</p>");
				}
			}
			else
				$this->CI->_add_content('<p>Which table?</p>');
		}
	}

	function _after_update() {
		$field=$this->_get_uri_field();
		$uri=$this->_create_uri($field);
		$this->newData['uri']=$uri;
		// trace_($this->newData);
		return $this->newData;
	}
	
	// extra methods for this plugin
	
	function _create_uri($uri_source_field) {
		$uri=$this->oldData['uri'];
		if (isset($this->newData[$uri_source_field]))
			$uri_source=$this->newData[$uri_source_field];
		else
			$uri_source=$this->newData['id'];
		// trace_($this->newData);
		// trace_($uri_source);
		if (empty($uri) or !($this->CI->cfg->get('CFG_table',$this->table,'b_freeze_uris')) ) {
			static $counter=1;
			$uri=strtolower($uri_source);
			$uri=strip_tags($uri);
			$uri=trim($uri);
			$uri=trim($uri,'-');
			$uri=str_replace(" ","_",trim($uri));
			$uri=clean_string($uri);
			$forbidden=array("site","sys","admin");
			if (in_array($uri,$forbidden)) $uri="_".$uri;
			while ($this->_existing_uri($uri)) $uri=$uri."_".$counter++;
		}
		return $uri;
	}
	
	function _existing_uri($uri) {
		if ($this->CI->db->field_exists('self_parent',$this->table) and isset($this->newData['self_parent'])) {
			$this->CI->db->select('self_parent');
			$this->CI->db->where('self_parent',$this->newData['self_parent']);
		}
		$this->CI->db->select("uri");
		$this->CI->db->where("uri",$uri);
		$this->CI->db->where("id !=",$this->id);
		$uris=$this->CI->db->get_result($this->table);
		if (empty($uris))
			return FALSE;
		return current($uris);
	}
	
	function _get_uri_field($fields='') {
		if (empty($fields)) {
			$fields=array_keys($this->oldData);
		}
		$uriField="";
		/**
		 * Auto uri field according to prefixes
		 */
		if (empty($uriField)) {
			$preTypes=$this->CI->config->item('URI_field_pre_types');
			$loop=true;
			while ($loop) {
				$field=current($fields);
				$pre=get_prefix($field);
				if (in_array($pre,$preTypes)) {
					$uriField=$field;
				}
				$field=next($fields);
				$loop=(empty($uriField) and $field!==FALSE);
			}
		}
		/**
		 * If still nothing set... just get the first field (after id,order and uri)
		 */
		if (empty($uriField)) {
			unset($fields["id"]);
			unset($fields["uri"]);
			unset($fields["order"]);
			unset($fields["self_parent"]);
			reset($fields);
			$uriField=current($fields);
		}
		return $uriField;
	}
	
	
}

?>