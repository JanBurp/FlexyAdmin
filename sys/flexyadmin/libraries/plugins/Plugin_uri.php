<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Reset uri velden in tabellen
 *
 * Geef: /plugin/uri/...table...
 *
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 */

class Plugin_uri extends Plugin {

  public function __construct() {
    parent::__construct();
  }

	public function _admin_api($args=NULL) {
		if (isset($args[0])) {
			$this->table=$args[0];
			if ($this->CI->db->table_exists($this->table) and $this->CI->db->field_exists('uri',$this->table)) {
        $this->CI->create_uri->set_table($this->table);
        $prefix=el(array('prefix',$this->table),$this->config,'');
        $this->CI->create_uri->set_prefix($prefix);
        $prefix_callback=el(array('prefix_callback',$this->table),$this->config,false);
        if ($prefix_callback) $this->CI->create_uri->set_prefix_callback($prefix_callback);
				// reset all uris of this table
				$allData = $this->CI->data->table($this->table)->get_result();
				foreach ($allData as $id => $data) {
					$this->id=$id;
					$this->oldData=$data;
					$this->newData=$data;
          // if (!isset($field)) $field=$this->_get_uri_field();
					$uri=$data['uri'];
					$newUri=$this->CI->create_uri->create($data,el(1,$args,false)); // reset
					if ($uri!==$newUri) {
            $this->CI->data->table($this->table);
						$this->CI->data->set('uri',$newUri);
						$this->CI->data->where('id',$id);
						$this->CI->data->update();
					}
				}
        $this->add_message("All uri's in <b>$this->table</b> are (re)set.</p><p>Just change one in this table to make sure all other plugins did there work.");
			}
		}
    return $this->show_messages();
	}
  
  
  public function _after_update() {
    $this->CI->create_uri->set_table($this->table);
    $prefix = el(array('prefix',$this->table),$this->config,'');
    $this->CI->create_uri->set_prefix($prefix);
    $prefix_callback = el(array('prefix_callback',$this->table),$this->config,false);
    if ($prefix_callback) $this->CI->create_uri->set_prefix_callback($prefix_callback);
		$uri = $this->CI->create_uri->create( $this->newData );
    if ($uri) $this->newData['uri']=$uri;
		return $this->newData;
	}
  


}

?>