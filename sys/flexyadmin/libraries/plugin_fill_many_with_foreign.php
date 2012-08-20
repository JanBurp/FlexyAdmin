<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Vult een relatie tabel met inhoud van een foreign tabel
 * 
 * Deze plugin komt van pas als je een tabel hebt die met een _foreign key_
 * verwijst naar een andere tabel en je wilt de _foreign key_ verwijzing
 * vervangen door een relatie tabel om zo meerdere keuzes mogelijk te maken.
 * 
 * Stel bijvoorbeeld dat in _tbl_menu_ het veld _id_links _bestaat:
 * 
 * - Maak (met de plugin **add_relation_table**) een relatie tabel aan: _rel_menu__links_
 * - Roep met `.../admin/plugin/fill_many_with_foreign/rel_menu_links/tbl_menu.id_links` deze plugin aan.
 * - _rel_menu_links_ is nu automatisch gevuld met verwijzingen die overeenkomen met de al bestaande foreign key verwijzingen van _tbl_menu.id_links_
 * - Nu kun je zonder problemen het veld _tbl_menu.id_links_ verwijderen.
 * 
 * @package default
 * @author Jan den Besten
 */
class Plugin_fill_many_with_foreign extends Plugin {

  /**
   * @ignore
   */
	function _admin_api($args=false) {
		if ($this->CI->user->is_super_admin()) {
			$this->add_content(h($this->name,1));
			$goodArgs=false;
			if ($args) {
				if (isset($args[0])) $relTable=$args[0];
				if (isset($args[1])) {
					$foreignKey=$args[1];
					$table=get_prefix($foreignKey,'.');
					$foreignKey=get_suffix($foreignKey,'.');
					$thisKey='id_'.get_suffix($table);
				}
				if (isset($relTable) and isset($table) and isset($foreignKey)) $goodArgs=true;
				$this->add_content(h("Filling '$relTable' from '$table.$foreignKey'.",2));
				// first emtpy many table
				$this->CI->db->truncate($relTable);
				// now fill
				$this->CI->db->select('id,'.$foreignKey);
				$data=$this->CI->db->get_result($table);
				foreach ($data as $id => $row) {
					$this->CI->db->set($thisKey,$id);
					$this->CI->db->set($foreignKey,$row[$foreignKey]);
					$this->CI->db->insert($relTable);
				}
			}
			if (!$goodArgs) {
				$this->add_content('<p>Which many table and foreign key?</br></br>Give: /rel_xxxx__xxxx/tbl_xxx.id_xxx</p>');
			}
		}
    return $this->content;
	}


}

?>