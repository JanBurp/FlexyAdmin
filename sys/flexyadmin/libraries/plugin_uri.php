<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 */


class Plugin_uri extends Plugin_ {

	function _admin_api($args=NULL) {
		$this->add_content(h($this->name,1));
		if (isset($args)) {
			if (isset($args[0])) {
				$this->table=$args[0];
				if ($this->CI->db->table_exists($this->table) and $this->CI->db->field_exists('uri',$this->table)) {

          $this->CI->create_uri->set_table($this->table);
					// reset all uris of this table
					$allData=$this->CI->db->get_results($this->table);
					foreach ($allData as $id => $data) {
						$this->id=$id;
						$this->oldData=$data;
						$this->newData=$data;
            // if (!isset($field)) $field=$this->_get_uri_field();
						$uri=$data['uri'];
						$newUri=$this->CI->create_uri->create($data);
						if ($uri!=$newUri) {
									$this->CI->db->set('uri',$newUri);
									$this->CI->db->where('id',$id);
									$this->CI->db->update($this->table);
								}
					}
					$this->add_content("<p>All uri's in $this->table are (re)set.</p><p>Just change one in this table to make sure all other plugins did there work.</p>");
				}
			}
			else
				$this->add_content('<p>Which table?</p>');
		}
	}

	function _after_update() {
    $this->CI->create_uri->set_table($this->table);
		$uri=$this->CI->create_uri->create($this->newData);
		$this->newData['uri']=$uri;
		return $this->newData;
	}
	
	// extra methods for this plugin
	
  // function _create_uri($uri_source_field) {
  //   $replaceSpace=$this->CI->config->item('PLUGIN_URI_REPLACE_CHAR');
  //   $uri=$this->oldData['uri'];
  //   if (isset($this->newData[$uri_source_field]))
  //     $uri_source=$this->newData[$uri_source_field];
  //   else
  //     $uri_source=$this->newData['id'];
  //   // trace_($this->newData);
  //   // trace_($uri_source);
  //   $createUri=true;
  //   if ($this->CI->cfg->get('CFG_table',$this->table,'b_freeze_uris')) $createUri=false;
  //   if (isset($this->newData['b_freeze_uri']) and $this->newData['b_freeze_uri']) $createUri=false;
  //   if (empty($uri)) $createUri=true;
  //   if ($createUri) {
  //     $uri=trim(strip_tags(strtolower($uri_source)),' -_');
  //     $uri=str_replace(" ",$replaceSpace,$uri);
  //     $uri=clean_string($uri);
  //     $forbidden=array("site","sys","admin","rss","file",'offset',':');
  //     $forbidden=array_merge($forbidden,$this->CI->config->item('LANGUAGES'));
  //     $postSpace=$replaceSpace.$replaceSpace;
  //     while ($this->_existing_uri($uri) or in_array($uri,$forbidden)) {
  //       $currUri=remove_suffix($uri,$postSpace);
  //       $countUri=(int) get_suffix($uri,$postSpace);
  //       $uri=$currUri.$postSpace.($countUri+1);
  //     }
  //   }
  //   return $uri;
  // }
  // 
  // function _existing_uri($uri) {
  //   if ($this->CI->db->field_exists('self_parent',$this->table) and isset($this->newData['self_parent'])) {
  //     $this->CI->db->select('self_parent');
  //     $this->CI->db->where('self_parent',$this->newData['self_parent']);
  //   }
  //   $this->CI->db->select("uri");
  //   $this->CI->db->where("uri",$uri);
  //   if (isset($this->newData['id'])) $this->CI->db->where("id !=",$this->newData['id']);
  //   $uris=$this->CI->db->get_result($this->table);
  //   if (empty($uris))
  //     return FALSE;
  //   return current($uris);
  // }
  // 
  // function _get_uri_field($fields='') {
  //   if (empty($fields)) {
  //     $fields=array_keys($this->oldData);
  //   }
  //   $uriField="";
  //   /**
  //    * Auto uri field according to prefixes
  //    */
  //   if (empty($uriField)) {
  //     $preTypes=$this->CI->config->item('URI_field_pre_types');
  //     $loop=true;
  //     while ($loop) {
  //       $field=current($fields);
  //       $pre=get_prefix($field);
  //       if (in_array($pre,$preTypes)) {
  //         $uriField=$field;
  //       }
  //       $field=next($fields);
  //       $loop=(empty($uriField) and $field!==FALSE);
  //     }
  //   }
  //   /**
  //    * If still nothing set... just get the first field (after id,order and uri)
  //    */
  //   if (empty($uriField)) {
  //     unset($fields["id"]);
  //     unset($fields["uri"]);
  //     unset($fields["order"]);
  //     unset($fields["self_parent"]);
  //     reset($fields);
  //     $uriField=current($fields);
  //   }
  //   return $uriField;
  // }
	
	
}

?>