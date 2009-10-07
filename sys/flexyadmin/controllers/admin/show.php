<?
require_once(APPPATH."controllers/admin/MY_Controller.php");

/**
 * FlexyAdmin V1
 *
 * A Flexible Database based CMS
 *
 * @package		FlexyAdmin V1
 * @author		Jan den Besten
 * @copyright	Copyright (c) 2008, Jan den Besten
 * @link			http://flexyadmin.com
 * @version		V1 0.1
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Show Controller Class
 *
 * This Controller shows a grid or form
 *
 * @package			FlexyAdmin V1
 * @author			Jan den Besten
 * @version			V1 0.1
 *
 */

class Show extends AdminController {
	
	var $form_args;

	function Show() {
		parent::AdminController();
		// $this->load->model("flexy_data","fd");
		$this->load->model("flexy_field","ff");
	}

	function index() {
		$this->_show_all();
	}

/**
 * This controls the order of a table
 *
 * @param string $table Table name
 * @param int $id maybe an id, the last that changed
 * @param mixed $newOrder (top|bottom|up|down|(number))
 */

	function order($table="",$id="",$newOrder="") {
		if (!empty($table) and ($id!="") and !empty($newOrder) and $this->has_rights($table,$id)>=RIGHTS_EDIT) {
			/**
			 * re-order data
			 */
			$this->lang->load("update_delete");
			$this->load->model("order");
			$this->order->set_to($table,$id,$newOrder);
			$this->set_message(langp("order_has_changed",$table));
			$this->load->model("login_log");
			$this->login_log->update($table);
			redirect(api_uri('API_view_grid',$table,$id));
		}
		$this->_show_all();
	}

	/**
	 * This controls the grid view
	 *
	 * @param string $table Table name
	 * @param mixed $id maybe an id, the last that changed
	 */

		function grid() {
			$args=$this->uri->uri_to_assoc();
			$table=el('grid',$args);
			$id=el('current',$args);
			$info=el('info',$args);
			
			if (!empty($table) and $this->db->table_exists($table)) {
				$singleRow=$this->cfg->get('CFG_table',$table,"b_single_row");
				if ($singleRow) {
					$this->db->select("id");
					$row=$this->db->get_row($table,1);
					$id=$row["id"];
					$this->form_args['form']=$table.':'.$id;
					$this->form();
					return;
				}
				else {
					if ($right=$this->has_rights($table,$id)) {
						$restrictedToUser=$this->user_restriction_id($table);
						$this->load->model("grid");
						$this->lang->load("help");
						$this->_add_js_variable("help_filter",$this->_add_help(langp('grid_filter')));
						$tableInfo=$this->cfg->get('CFG_table',$table);
					
						/**
						 * get data
						 */
						
						// extra info?
						if (!empty($info)) {
							// yes, get extra query info
							$extraInfo=$this->cfg->get('cfg_admin_menu',$info);
							$where=$extraInfo['str_table_where'];
							if (!empty($where));
							$this->db->where($where,NULL,FALSE);
							$uiTable=$extraInfo['str_ui_name'];
							// trace_($extraInfo);
						}
						if ($this->db->has_field($table,"self_parent")) {
							$this->db->order_as_tree();
						}
						if ($restrictedToUser>0 and $this->db->has_field($table,"user")) {
							$this->db->where($table.".user",$restrictedToUser);
							$this->db->dont_select("user");
						}
						if ($table=="cfg_users")
							$this->db->where("id >=",$this->user_id);
							
						$this->db->add_foreigns_as_abstracts();
						if ($tableInfo['b_grid_add_many']) $this->db->add_many();
						$this->db->max_text_len(250);
						$data=$this->db->get_result($table);
						// trace_($data);
						if (empty($data)) {
							/**
							 * if no data, start an input form
							 */
							$this->form_args['form']=$table.':-1';
							$this->form();
							return;
						}
						else
						{
							$this->_before_grid($table,$data);

							/**
							 * if data: first render data, then put data in grid and render as html
							 */
							if ($right<RIGHTS_EDIT) {
								// remove order fields
								foreach ($data as $id => $row) unset($data[$id]['order']);
							}
							$data=$this->ff->render_grid($table,$data,$right,$info);

							$grid=new grid();
							if (empty($uiTable)) $uiTable=$this->uiNames->get($table);
							$tableHelp=$this->cfg->get("CFG_table",$table,"txt_help");
							if (!empty($tableHelp)) {
								$uiShowTable=help($uiTable." ",$tableHelp);
							}
							else
								$uiShowTable=$uiTable;
							$grid->set_data($data,$uiShowTable);
							$keys=array_keys(current($data));
							$keys=combine($keys,$keys);
							if ($right>=RIGHTS_ADD) {
								$newUri=api_uri('API_view_form',$table.':-1');
								if (!empty($info)) $newUri.='/info/'.$info;
								$newIcon=anchor($newUri,help(icon("new"),langp('grid_new',$uiTable)) );
								if ($this->cfg->get('CFG_table',$table,'int_max_rows')<count($data))
									$grid->prepend_to_captions($newIcon,"new");
								else
									$grid->prepend_to_captions('&nbsp;');
							}
							$grid->set_headings($this->uiNames->get($keys,$table));
							$grid->set_heading(pk(),"Edit");
							if (!empty($id)) {
								$grid->set_current($id);
							}
							$html=$grid->view("html",$table,"grid");
							$this->_set_content($html);
						}
					}
				}
				$this->_show_type("grid");
			}
			if (!isset($uiTable)) $uiTable="";
			$this->_show_all($uiTable);
		}


/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */

	function form($table='') {
		if (isset($this->form_args)) {
			$args=$this->form_args;
		}
		else {
			$args=$this->uri->uri_to_assoc();
		}
		$table=el('form',$args);
		$info=el('info',$args);
		$table=explode(':',$table);
		$id=el(1,$table);
		$table=el(0,$table);

		if (!empty($table) and ($id!="")
				and $this->db->table_exists($table)
				and $right=$this->has_rights($table,$id)) {
			$restrictedToUser=$this->user_restriction_id($table);
			
			$this->load->library('form_validation');
			$this->load->library('upload');
			$this->load->model("order");
			$this->load->helper('html');
			$this->lang->load("form");

			$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
			$this->load->model("form");

			/**
			 * get data
			 */
			if (get_prefix($table)!=$this->config->item('REL_table_prefix')) {
				$this->db->add_many();
				// $this->db->add_foreigns();
			}
			$this->db->add_options();
			if ($id==-1) {
				// New item, fill data with defaults
				$data=$this->db->defaults($table);
				$options=el("options",$data);
				$multiOptions=el("multi_options",$data);
				$data=current($data);
				$data[pk()]="-1";
			}
			else {
				if ($restrictedToUser>0 and $this->db->has_field($table,"user")) {
					$this->db->where("user",$restrictedToUser);
					$this->db->dont_select("user");
				}
				if ($id!="") $this->db->where($table.".".pk(),$id);
				$data=$this->db->get_result($table);
				// trace_($data);
				$options=el("options",$data);
				$multiOptions=el("multi_options",$data);
				$data=current($data);
			}
						
			/**
			 * if data: first render data for the form class, then put data in form
			 */
			if (!empty($data)) {
				$this->ff->set_restricted_to_user($restrictedToUser,$this->user_id);
				$ffData=$this->ff->render_form($table,$data,$options,$multiOptions);

				$actionUri=api_uri('API_view_form',$table.':'.$id);
				if (!empty($info)) $actionUri.='/info/'.$info;
				$form=new form($actionUri);

				if (!empty($info))
					$uiTable=$this->cfg->get('cfg_admin_menu',$info,'str_ui_name');
				else
					$uiTable=$this->uiNames->get($table);
				$tableHelp=$this->cfg->get("CFG_table",$table,"txt_help");
				if (!empty($tableHelp)) {
					$uiShowTable=help($uiTable,$tableHelp);
				}
				else
					$uiShowTable=$uiTable;
				$form->set_data($ffData,$uiShowTable);
				// trace_($ffData);

				/**
				 * Validate form, if succes, make form do an update
				 */
				if ($form->validation()) {
					$this->lang->load("update_delete");
					$redirectUri=api_uri('API_view_grid',$table);
					if (!empty($info)) $redirectUri.='/info/'.$info;
					
					if ($this->_has_key($table)) {
						$resultId=$form->update($table,$restrictedToUser);
						if ($id==-1)
							$this->_after_update($table,$resultId);
						else
							$this->_after_update($table,$resultId,$data);
						if (is_string($resultId)) {
							$this->set_message(langp("update_error",$table,$resultId));
							redirect($redirectUri);
						}
						else {
							if ($id==-1)
								$this->set_message(langp("insert_new",$table));
							else
								$this->set_message(langp("update_succes",$table));
							$this->load->model("login_log");
							$this->login_log->update($table);
							redirect($redirectUri.'/current/'.$resultId);
						}
					}
					else
						$this->_add_content('<p class="error">'.$this->_no_key($table).'</p>');
				}

				/**
				 * Validate form, no succes: show form, maybe with validation errors
				 */
				else {
					$this->_add_content(validation_errors());

					$keys=array_keys($ffData);
					$keys=combine($keys,$keys);
					$uiFieldNames=array();
					foreach($keys as $key) {
						$fieldHelp=$this->cfg->get("CFG_field",$table.".".$key,"txt_help");
						if (!empty($fieldHelp))
							$uiFieldNames[$key]=help($this->uiNames->get($key,$table),$fieldHelp);
						else
							$uiFieldNames[$key]=$this->uiNames->get($key,$table);
					}
					$form->set_labels($uiFieldNames);
					
					if ($right<RIGHTS_EDIT) $form->no_submit();
					$html=$form->render("html",$table);
					if ($form->has_htmlfield()) $this->use_editor();
					$this->_add_content($html);
				}
				$this->_show_type("form");
			}
		}
		/**
		 * output
		 */
		if (!isset($uiTable)) $uiTable="";
		$this->_show_all($uiTable);
	}


/**
 * This controls the user settings
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */

	function user() {
		/**
		 * get user data
		 */
		$userTable=$this->config->item('CFG_table_prefix')."_".$this->config->item('CFG_users');
		$userId=$this->session->userdata("user_id");
		// $this->form($userTable,$userId);
		
		$this->db->select("id,str_user_name,gpw_user_pwd,str_language");
		$this->db->add_options();
		// $this->db->add_many();
		$this->db->where("id",$userId);
		$userData=$this->db->get_result($userTable);
		$options=el("options",$userData);
		$userData=current($userData);
		
		/**
		 * Init user form
		 */
		
		$formData=$this->ff->render_form($userTable,$userData,$options);

		$this->lang->load("update_delete");
		$this->lang->load("form");

		$this->load->library('form_validation');
		$this->load->helper('html');
		$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
		$this->load->model("form");
		$form=new form(api_uri('API_user'));
		$form->set_data($formData,$userData["str_user_name"]);
		/**
		 * Validate form, if succes, make form do an update
		 */
		if ($form->validation()) {
			$resultId=$form->update($userTable);
			if (is_string($resultId)) {
				$this->set_message(langp("update_error",$userTable,$resultId));
				redirect(api_uri('API_home'));
			}
			else {
				$form->update($userTable);
				$this->set_message(lang("update_user_changed"));
				$this->load->model("login_log");
				$this->login_log->update($userTable);
				// reset user session
				$this->db->where("id",$userId);
				$this->db->select("str_user_name,str_language");
				$query=$this->db->get($userTable);
				$userData=$query->row_array();
				//trace_($userData);
				$this->session->set_userdata("user",$userData["str_user_name"]);
				$this->session->set_userdata("language",$userData["str_language"]);
				redirect(api_uri('API_home'));
			}
		}
		else {
			/**
			 * Render
			 */
			$html=$form->render("html");
			$this->_add_content($html);
			$this->_show_type("form");
		}
		$this->_show_all();
	}


/**
 * Here are some form validation callback functions
 * Routings are set so that admin/show/valid_* is routed to admin/show, so these callbacks are not reached by url
 */

	function valid_rgb($rgb) {
		$rgb=trim($rgb);
		if (empty($rgb)) {
			return TRUE;
		}
		$rgb=str_replace("#","",$rgb);
		$len=strlen($rgb);
		if ($len!=3 and $len!=6) {
			$this->lang->load("form_validation");
			$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
			return FALSE;
		}
		$rgb=strtoupper($rgb);
		if (ctype_xdigit($rgb))
			return "#$rgb";
		else {
			$this->lang->load("form_validation");
			$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
			return FALSE;
		}
	}

}

?>
