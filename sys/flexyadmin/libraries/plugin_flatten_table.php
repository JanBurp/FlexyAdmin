<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt één tabel van meerdere tabellen die aan elkaar gekoppeld zijn met een foreign key
 * 
 * Eerste argument is de tabel, de andere tabellen hoeven niet meegegeven te worden, die worden vanzelf gevonden aan de hand van de primary keys.
 * Alleen de velden die in beidde tabellen bestaan worden overgenomen. Dus maak in de meegegeven tabel de velden aan die moeten worden gevuld.
 *  
 *      .../admin/plugin/flatten_table/tbl_menu
 * 
 * @package default
 * @author Jan den Besten
 */
 
class Plugin_flatten_table extends Plugin {

	public function __construct() {
		parent::__construct();
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
				if (isset($table)) $goodArgs=true;
				if ($goodArgs) {
          $data=$this->CI->db->get_result($table);
          $fields=$this->CI->db->list_fields($table);
          $foreign_keys=filter_by($fields,'id_');
          foreach ($data as $id => $row) {
            foreach ($foreign_keys as $fkey) {
              $foreignTable=foreign_table_from_key($fkey);
              $this->CI->db->where('id',$row[$fkey]);
              $foreignData=$this->CI->db->get_row($foreignTable);
              unset($foreignData['id']);
              $set=array();
              foreach ($foreignData as $field => $value) {
                if (isset($row[$field])) {
                  $set[$field]=$value;
                }
              }
              if (!empty($set)) {
                $this->CI->db->set($set)->where('id',$id)->update($table);
                $this->add_content(p()."updated $table.$id"._p());
              }
            }
          }
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Flatten table\'s, which table?</br></br>Give: /tbl_xxx</p>');
			}
      return $this->content;
		}
	}
	
}

?>