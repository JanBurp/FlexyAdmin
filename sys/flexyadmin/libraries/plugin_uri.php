<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * FlexyAdmin Plugin
 *
 * @author Jan den Besten
 * @ignore
 * @internal
 */

class Plugin_uri extends Plugin {

  public function __construct() {
    parent::__construct();
  }

	public function _admin_api($args=NULL) {
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
          $this->add_message("All uri's in <b>$this->table</b> are (re)set.</p><p>Just change one in this table to make sure all other plugins did there work.");
				}
			}
			else
				$this->add_message('Which table?');
		}
    
    return $this->view('admin/plugins/plugin');
	}



	public function _after_update() {
    $this->CI->create_uri->set_table($this->table);
		$uri=$this->CI->create_uri->create($this->newData);
		$this->newData['uri']=$uri;
		return $this->newData;
	}
	
}

?>