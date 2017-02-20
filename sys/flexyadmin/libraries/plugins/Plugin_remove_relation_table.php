<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * Verwijderd een relatie tabel.
 * 
 * Deze plugin verwijderd de meegegeven relatie tabel.
 * De kopeling wordt voor de 1e verwijzing die gevonden wordt behouden door in 'linkertabel' een foreignkey aan te maken (als die er nog niet is).
 * 
 *      ../_admin/plugin/remove_relation_table/rel_menu__links
 * 
 * @author Jan den Besten
 */
 
class Plugin_remove_relation_table extends Plugin {

	public function __construct() {
		parent::__construct();
	}

	
  /**
   */
   function _admin_api($args=false) {
		if ($this->CI->flexy_auth->is_super_admin()) {

			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $rel_table=$args[0];
				if (isset($rel_table) and $this->CI->db->table_exists($rel_table)) $goodArgs=true;
				if ($goodArgs) {
          
          $this->CI->load->dbforge();
          
          $table = table_from_rel_table($rel_table);
          $foreign_key_table = foreign_key_from_table($table);
          $join_table = join_table_from_rel_table($rel_table);
          $foreign_key_join = foreign_key_from_table($join_table);
          
          // trace_([$rel_table,$table,$foreign_key_table,$join_table,$foreign_key_join]);
          
          // Maak foreign key aan als die er nog niet is
          if ( ! $this->CI->db->field_exists($foreign_key_join,$table)) {
            $this->CI->dbforge->add_column($table, array($foreign_key_join=>array('type'=>'INT')));
            $this->add_content("<p><strong>Created '$table.$foreign_key_join'</strong></p>");
          }
          
          // Pak de huidige data, en voer die in bij de foreign keys
          $this->CI->data->table( $rel_table )->set_result_key($foreign_key_table);
          $rel_data = $this->CI->data->get_result();
          foreach ($rel_data as $id_table => $row) {
            $this->CI->data->table($table);
            $this->CI->data->where(PRIMARY_KEY,$id_table);
            $this->CI->data->set($foreign_key_join,$row[$foreign_key_join]);
            $this->CI->data->update();
            $this->add_content("<p>Updated item: ".$table."[".$id_table."]</p>");
          }
          
          // DELETE rel_table
          $this->CI->dbforge->drop_table($rel_table,true);
          $this->add_content("<p><strong>DELETED '$rel_table'</strong></p>");
          
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Remove relation table, which (existing) relation table?</br></br>Give: /rel_xxx__xxx</p>');
			}
		}
    return $this->view('admin/plugins/plugin');
	}

}

?>