<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_uri extends Plugin_ {

	function init($init=array()) {
		parent::init($init);
		$this->act_on(array('fields'=>'uri'));
	}

	function _admin_api($args=NULL) {
		$this->_add_content(h($this->plugin,1));
		if (isset($args)) {
			if (isset($args[0])) {
				$this->table=$args[0];
				if ($this->db->table_exists($this->table) and $this->db->field_exists('uri',$this->table)) {
					// reset all uris of this table
					$allData=$this->db->get_results($this->table);
					foreach ($allData as $id => $data) {
						$this->id=$id;
						$this->oldData=$data;
						$this->newData=$data;
						if (!isset($field)) $field=$this->_get_uri_field();
						$uri=$data['uri'];
						$newUri=$this->_create_uri($field);
						if ($uri!=$newUri) {
									$this->db->set('uri',$newUri);
									$this->db->where('id',$id);
									$this->db->update($this->table);
								}
					}
					$this->_add_content("<p>All uri's in $this->table are (re)set.</p><p>Just change one in this table to make sure all other plugins did there work.</p>");
				}
			}
			else
				$this->_add_content('<p>Which table?</p>');
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
		$replaceSpace=$this->config->item('PLUGIN_URI_REPLACE_CHAR');
		$uri=$this->oldData['uri'];
		if (isset($this->newData[$uri_source_field]))
			$uri_source=$this->newData[$uri_source_field];
		else
			$uri_source=$this->newData['id'];
		// trace_($this->newData);
		// trace_($uri_source);
		$createUri=true;
		if ($this->cfg->get('CFG_table',$this->table,'b_freeze_uris')) $createUri=false;
		if (isset($this->newData['b_freeze_uri']) and $this->newData['b_freeze_uri']) $createUri=false;
		if (empty($uri)) $createUri=true;
		if ($createUri) {
			$uri=trim(strip_tags(strtolower($uri_source)),' -_');
			$uri=str_replace(" ",$replaceSpace,$uri);
			$uri=clean_string($uri);
			$forbidden=array("site","sys","admin","rss","file",'offset');
			$forbidden=array_merge($forbidden,$this->config->item('LANGUAGES'));
			$postSpace=$replaceSpace.$replaceSpace;
			while ($this->_existing_uri($uri) or in_array($uri,$forbidden)) {
				$currUri=remove_postfix($uri,$postSpace);
				$countUri=(int) get_postfix($uri,$postSpace);
				$uri=$currUri.$postSpace.($countUri+1);
			}
		}
		return $uri;
	}
	
	function _existing_uri($uri) {
		if ($this->db->field_exists('self_parent',$this->table) and isset($this->newData['self_parent'])) {
			$this->db->select('self_parent');
			$this->db->where('self_parent',$this->newData['self_parent']);
		}
		$this->db->select("uri");
		$this->db->where("uri",$uri);
		$this->db->where("id !=",$this->id);
		$uris=$this->db->get_result($this->table);
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
			$preTypes=$this->config->item('URI_field_pre_types');
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