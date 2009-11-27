<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH."libraries/plugin.php");

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class plugin_uri extends plugin {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('fields'=>'uri'));
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
		$uri_source=$this->newData[$uri_source_field];
		// trace_($this->newData);
		// trace_($uri_source);
		if (empty($uri) or !($this->CI->cfg->get('CFG_table',$this->table,'b_freeze_uris')) ) {
			static $counter=1;
			$uri=strtolower($uri_source);
			$uri=strip_tags($uri);
			$uri=str_replace(" ","_",trim($uri));
			$uri=clean_string($uri);
			$forbidden=array("site","sys","admin");
			if (in_array($uri,$forbidden)) $uri="_".$uri;
			while ($this->_existing_uri($uri)) $uri=$uri."_".$counter++;
		}
		return $uri;
	}
	
	function _existing_uri($uri) {
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