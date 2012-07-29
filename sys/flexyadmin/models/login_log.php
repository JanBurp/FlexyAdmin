<?
/**
 * logs admin logins
 *
 * @package default
 * @author Jan den Besten
 * @ignore
 * @internal
 */
 class Login_log extends CI_Model {

	function update($table) {
		if ($this->session->userdata("user_id")!==FALSE) {
			$user_id=$this->session->userdata("user_id");
			// get changed tables field
			$this->db->select("id,str_changed_tables");
			$this->db->where("id_user",$user_id);
			$this->db->order_by("tme_login_time DESC");
			$query=$this->db->get($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'));
			$row=$query->row();
			$query->free_result();
			if (!empty($row)) {
				$log_id=$row->id;
				$changedTables=$row->str_changed_tables;
				// add this changed table, if its not there now
				if (strpos($changedTables,$table)===FALSE) {
					$changedTables=add_string($changedTables,$table);
					// update
					$this->db->where("id",$log_id);
					$this->db->set("str_changed_tables",$changedTables);
					$this->db->update($this->config->item('LOG_table_prefix')."_".$this->config->item('LOG_login'));
				}
			}
		}
	}

 }
?>
