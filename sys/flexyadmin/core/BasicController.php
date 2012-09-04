<?

/**
 * BasicController Class extends MY_Controller
 *
 * Same as MY_Controller
 *
 * @package FlexyAdmin V1
 * @author Jan den Besten
 * @version V1 0.1
 * @ignore
 * @internal
 */

class BasicController extends MY_Controller {

	var $user_name;
	var $user_id;
	var $language;
	var $plugins;

	function __construct($isAdmin=false) {
		parent::__construct($isAdmin);
		$this->load->library('session');
		$this->load->library('user');
		
		if ( ! $this->_user_logged_in()) {
			redirect($this->config->item('API_login'));
		}

		// ok move on...
		$this->load->model('plugin_handler');
    $this->load->model('message');
    $this->message->init();
    $this->load->model('create_uri');
		$this->load->helper("language");

		$lang=$this->language."_".strtoupper($this->language);
		setlocale(LC_ALL, $lang);

		$this->plugin_handler->init_plugins();
	}

	function _user_logged_in() {
		$logged_in = $this->user->logged_in();
		if ($logged_in) {
			$this->user_id=$this->session->userdata("user_id");
			$this->user_name=$this->session->userdata("str_username");
			$this->language=$this->session->userdata("language");
			$this->user_group_id=$this->session->userdata("id_user_group");
		}
		return $logged_in;
	}

	function _update_links_in_txt($oldUrl,$newUrl="") {
		// loop through all txt fields..
		$tables=$this->db->list_tables();
		foreach($tables as $table) {
			if (get_prefix($table)==$this->config->item('TABLE_prefix')) {
				$fields=$this->db->list_fields($table);
				foreach ($fields as $field) {
					if (get_prefix($field)=="txt") {
						$this->db->select("id,$field");
						$this->db->where("$field !=","");
						$query=$this->db->get($table);
						foreach($query->result_array() as $row) {
							$thisId=$row["id"];
							$txt=$row[$field];
							if (empty($newUrl)) {
								// remove
								$pattern='/<a(.*?)href="'.str_replace("/","\/",$oldUrl).'"(.*?)>(.*?)<\/a>/';
								$txt=preg_replace($pattern,'\\3',$txt);
							}
							else {
								$txt=str_replace("href=\"$oldUrl","href=\"$newUrl",$txt);
							}
							$res=$this->db->update($table,array($field=>$txt),"id = $thisId");
						}
						$query->free_result();
					}
				}
			}
		}
	}

	/**
	 * Here are functions that hook into the grid/form/update proces.
	 * They check if a standard hook method for the current table/field/id, if so call it
	 */


	function _get_parent_uri($table,$uri,$parent) {
		if ($parent!=0) {
			$this->db->select('id,uri,self_parent');
			$this->db->where(PRIMARY_KEY,$parent);
			$parentRow=$this->db->get_row($table);
			$uri=$parentRow['uri']."/".$uri;
			if ($parentRow['self_parent']!=0) $uri=$this->_get_parent_uri($table,$uri,$parentRow['self_parent']);
		}
		return $uri;
	}


	function _init_plugin($table,$oldData=NULL,$newData=NULL) {
		$this->plugin_handler->set_data('old',$oldData);
		$this->plugin_handler->set_data('new',$newData);
		$this->plugin_handler->set_data('table',$table);
	}

	function _before_grid($table) {
		$this->_init_plugin($table,NULL,NULL);
		return $this->plugin_handler->call_plugins_before_grid_trigger();
	}

	function _after_delete($table,$oldData=NULL) {
		$this->_init_plugin($table,$oldData,NULL);
		return $this->plugin_handler->call_plugins_after_delete_trigger();
	}
	
	function _after_update($table,$oldData=NULL,$newData=NULL) {
		$this->_init_plugin($table,$oldData,$newData);
		$newData=$this->plugin_handler->call_plugins_after_update_trigger();
		return $newData;
	}

}

?>