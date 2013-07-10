<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt van één tabel twee tabellen door een veld te verhuizen naar een foreign table gekoppeld met een foreignkey
 * 
 * Eerste argument is de tabel, tweede argument is het veld wat moet verhuizen.
 *  
 *      .../admin/plugin/create_foreign/tbl_menu/str_link
 * 
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_create_foreign extends Plugin {

	public function __construct() {
		parent::__construct();
    $this->CI->load->dbforge();
	}

	
  /**
   * @ignore
   */
   function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $table=$args[0];
        if (isset($args[1])) $field=$args[1];
				if (isset($table) and isset($field)) $goodArgs=true;
				if ($goodArgs) {
          $foreign_key='id_'.remove_prefix($field);
          $foreign_table='tbl_'.remove_prefix($field);
          // Create Foreign table
          $this->CI->dbforge->add_field('id');
          $this->CI->dbforge->add_field('`str_groep` VARCHAR(255) CHARACTER SET utf8 NOT NULL DEFAULT \'\'');
          $this->CI->dbforge->create_table($foreign_table,TRUE);
          // Create foreign_key
          if (!$this->CI->db->field_exists($foreign_key,$table)) $this->CI->dbforge->add_column($table, array( $foreign_key => array('type'=>'INT')) );
          
          // Fill
          $data=$this->CI->db->get_result($table);          
          foreach ($data as $id => $row) {
            // Does foreign data exists?
            $value=$row[$field];
            $fid=$this->CI->db->get_field_where($foreign_table,'id',$field,$value);
            if (!$fid) {
              // No: add data to foreign_table
              $this->CI->db->set($field,$value)->insert($foreign_table);
              $fid=$this->CI->db->insert_id();
            }
            // Connect
            $this->CI->db->set($foreign_key,$fid)->where('id',$id)->update($table);
            $this->add_content('<p>Connected: '.$fid.' => '.$value.'</p>');
          }
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Which table and Field?</br></br>Give: /tbl_xxx/str_xxxx</p>');
			}
      return $this->content;
		}
	}
	
}

?>