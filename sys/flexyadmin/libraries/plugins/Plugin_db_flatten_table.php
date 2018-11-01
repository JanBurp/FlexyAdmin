<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Maakt één tabel van meerdere tabellen die aan elkaar gekoppeld zijn met een foreign key
 * 
 * Eerste argument is de tabel, de andere tabellen hoeven niet meegegeven te worden, die worden vanzelf gevonden aan de hand van de primary keys.
 * Alleen de velden die in beide tabellen bestaan worden overgenomen. Dus maak in de meegegeven tabel de velden aan die moeten worden gevuld.
 *  
 *      ../_admin/plugin/flatten_table/tbl_menu
 * 
 * @author Jan den Besten
 */
 
class Plugin_db_flatten_table extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	
  /**
   */
   function _admin_api($args=false) {
		if ($this->CI->flexy_auth->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $table=$args[0];
				if (isset($table)) $goodArgs=true;
				if ($goodArgs) {
          $data=$this->CI->data->table($table)->get_result();
          $fields=$this->CI->data->list_fields();
          $foreign_keys=filter_by($fields,'id_');
          foreach ($data as $id => $row) {
            foreach ($foreign_keys as $fkey) {
              $foreignTable=foreign_table_from_key($fkey);
              $foreignData=$this->CI->data->table($foreignTable)->where('id',$row[$fkey])->get_row();
              unset($foreignData['id']);
              $set=array();
              if ($foreignData) {
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
			}
			if (!$goodArgs) {
				$this->add_content('<p>Flatten table\'s, which table?</br></br>Give: /tbl_xxx</p>');
			}
      return $this->content;
		}
	}
	
}

?>