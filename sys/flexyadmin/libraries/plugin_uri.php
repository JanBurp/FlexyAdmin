<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_uri extends Plugin {

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
	
}

?>