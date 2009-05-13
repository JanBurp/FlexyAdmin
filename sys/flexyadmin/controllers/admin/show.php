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

	function Show() {
		parent::AdminController();
		// $this->load->model("flexy_data","fd");
		$this->load->model("flexy_field","ff");
	}

	function index() {
		$this->_set_content("VIEW");
		$this->_show_all();
	}

/**
 * This controls the order of a table
 *
 * @param string $table Table name
 * @param int $id maybe an id, the last that changed
 * @param mixed $newOrder (top|bottom|up|down|(number))
 */

	function order($table,$id="",$newOrder="") {
		if ($this->has_rights($table,$id)>=RIGHTS_EDIT) {
			/**
			 * re-order data
			 */
			if (!empty($table) and !empty($id) and !empty($newOrder)) {
				$this->lang->load("update_delete");
				$this->load->model("order");
				$this->order->set_to($table,$id,$newOrder);
				$this->set_message(langp("order_has_changed",$table));
				$this->load->model("login_log");
				$this->login_log->update($table);
				redirect(api_uri('API_view_grid',$table,$id));
			}
		}
		else {
			$this->lang->load("rights");
			$this->set_message(lang("rights_no_rights"));
			$uiTable="";
			/**
			 * show
			 */
			$this->_show_all($uiTable);
		}
	}


/**
 * This controls the tree view
 *
 * @param string $table Table name
 * @param mixed $id maybe an id, the last that changed
 */

	// function tree($table,$id="") {
	// 	$singleRow=$this->cfg->get('CFG_table',$table,"b_single_row");
	// 	if ($singleRow)
	// 		$this->form($table);
	// 	else {
	// 		if ($this->has_rights($table,$id)) {
	// 			/**
	// 			 * get data
	// 			 */
	// 			// $this->db->add_foreigns_as_abstracts();
	// 			// $this->db->add_many();
	// 			$this->db->max_text_len(250);
	// 			$data=$this->db->get_result($table);
	// 			// trace_($data);
	// 			if (empty($data)) {
	// 				/**
	// 				 * if no data, start an input form
	// 				 */
	// 				 $this->form($table,-1);
	// 				 return;
	// 			}
	// 			else
	// 			{
	// 				$this->_before_grid($table,$data);
	// 				
	// 				/**
	// 				 * if data: first render data, then put data in grid and render as html
	// 				 */
	// 				$this->load->model("tree");
	// 				$tree=new tree();
	// 				
	// 				$innerTree=new menu();
	// 				$innerTree->set_current($id);
	// 				$innerTree->set_url_field("id");
	// 				$innerTree->set_url_template(api_uri('API_view_form',$table,"%s"));
	// 				$innerTree->set_menu_from_table($table);
	// 				// $innerTree->add_controls(	anchor(api_uri('API_view_form',$table,"%s"),icon("edit"),array("class"=>"edit")).
	// 				// 													anchor(api_uri('API_confirm',$table,"%s"),icon("delete"),array("class"=>"delete"))	);
	// 				$html=$innerTree->render();
	// 				$uiTable=$this->uiNames->get($table);
	// 				$tableHelp=$this->cfg->get("CFG_table",$table,"txt_help");
	// 				if (!empty($tableHelp)) {
	// 					$uiShowTable=help($uiTable." ",$tableHelp);
	// 				}
	// 				else
	// 					$uiShowTable=$uiTable;
	// 				$tree->set_tree($html,$uiTable);
	// 				
	// 				$newIcon=anchor(api_uri('API_view_form',$table,-1),icon("new"));
	// 				$tree->prepend_to_captions($newIcon,"new");
	// 				if (!empty($id)) {
	// 					$tree->set_current($id);
	// 				}
	// 				$renderData=$tree->render($table,"tree");
	// 				// trace_($renderData);
	// 				$html=$this->load->view("admin/tree",$renderData,true);
	// 				$this->_set_content($html);
	// 			}
	// 		}
	// 		else {
	// 			$this->lang->load("rights");
	// 			$this->set_message(lang("rights_no_rights"));
	// 			$uiTable="";
	// 		}
	// 
	// 		/**
	// 		 * show
	// 		 */
	// 		$this->_show_type("tree");
	// 		$this->_show_all($uiTable);
	// 	}
	// }


	/**
	 * This controls the grid view
	 *
	 * @param string $table Table name
	 * @param mixed $id maybe an id, the last that changed
	 */

		function grid($table,$id="") {
			$singleRow=$this->cfg->get('CFG_table',$table,"b_single_row");
			if ($singleRow)
				$this->form($table);
			else {
				if ($right=$this->has_rights($table,$id)) {
					$restrictedToUser=$this->user_restriction_id($table);
					$this->load->model("grid");
					/**
					 * get data
					 */
					if ($this->db->has_field($table,"self_parent")) {
						$this->db->order_as_tree();
					}
					if ($restrictedToUser>0 and $this->db->has_field($table,"user")) {
						$this->db->where("user",$restrictedToUser);
						$this->db->dont_select("user");
					}
					$this->db->add_foreigns_as_abstracts();
					$this->db->add_many();
					$this->db->max_text_len(250);
					$data=$this->db->get_result($table);
					// trace_($data);
					if (empty($data)) {
						/**
						 * if no data, start an input form
						 */
						 $this->form($table,-1);
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
						$data=$this->ff->render_grid($table,$data,$right);

						$grid=new grid();
						$uiTable=$this->uiNames->get($table);
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
							$newIcon=anchor(api_uri('API_view_form',$table,-1),icon("new"));
							$grid->prepend_to_captions($newIcon,"new");
						}
						$grid->set_headings($this->uiNames->get($keys,$table));
						$grid->set_heading(pk(),"Edit");
						if (!empty($id)) {
							$grid->set_current($id);
						}
						$renderData=$grid->render("html",$table,"grid");
						$html=$this->load->view("admin/grid",$renderData,true);
						$this->_set_content($html);
					}
				}
				else {
					$this->lang->load("rights");
					$this->set_message(lang("rights_no_rights"));
					$uiTable="";
				}

				/**
				 * show
				 */
				$this->_show_type("grid");
				$this->_show_all($uiTable);
			}
		}




/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */

	function form($table,$id="") {
		if ($right=$this->has_rights($table,$id)) {
			$restrictedToUser=$this->user_restriction_id($table);
			
			$this->load->library('form_validation');
			$this->load->library('upload');
			$this->load->model("order");
			$this->load->helper('html');

			$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
			$this->load->model("form");
			/**
			 * get data
			 */
			$this->db->add_many();
			$this->db->add_options();
			if ($restrictedToUser>0 and $this->db->has_field($table,"user")) {
				$this->db->where("user",$restrictedToUser);
				$this->db->dont_select("user");
			}
			if ($id!="") $this->db->where($table.".".pk(),$id);
			$data=$this->db->get_result($table);
			$options=el("options",$data);
			$multiOptions=el("multi_options",$data);
			$data=current($data);

			/**
			 * if no data, new item: fill data with default values
			 */
			if (empty($data)) {
				$this->db->add_many();
				$this->db->add_foreigns();
				$this->db->add_options();
				$data=$this->db->defaults($table);
				$options=el("options",$data);
				$multiOptions=el("multi_options",$data);
				$data=current($data);
				$data[pk()]="-1";
				$id=-1;
			}

			/**
			 * if data: first render data for the form class, then put data in form
			 */

			$this->ff->set_restricted_to_user($restrictedToUser,$this->user_id);
			$data=$this->ff->render_form($table,$data,$options,$multiOptions);

			$form=new form(api_uri('API_view_form',$table,$id));
			$uiTable=$this->uiNames->get($table);
			$tableHelp=$this->cfg->get("CFG_table",$table,"txt_help");
			if (!empty($tableHelp)) {
				$uiShowTable=help($uiTable,$tableHelp);
			}
			else
				$uiShowTable=$uiTable;
			$form->set_data($data,$uiShowTable);

			/**
			 * Validate form, if succes, make form do an update
			 */
			if ($form->validation()) {
				$this->lang->load("update_delete");
				if ($this->_has_key($table)) {
					$resultId=$form->update($table,$restrictedToUser);
					if (is_string($resultId)) {
						$this->set_message(langp("update_error",$table,$resultId));
						redirect(api_uri('API_view_grid',$table));
					}
					else {
						if ($id==-1)
							$this->set_message(langp("insert_new",$table));
						else
							$this->set_message(langp("update_succes",$table));
						$this->load->model("login_log");
						$this->login_log->update($table);
						redirect(api_uri('API_view_grid',$table,$resultId));
					}
				}
				else
					$this->set_message($this->_no_key($table));
			}
			/**
			 * Validate form, no succes: show form, maybe with validation errors
			 */
			else {
				$this->_add_content(validation_errors());

				$keys=array_keys($data);
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
				if ($right<RIGHTS_EDIT) $form->show_submit(FALSE);
				$html=$form->render("html",$table);
				if ($form->has_htmlfield()) $this->use_editor();
				$this->_add_content($html);
			}
		}
		else {
			$this->lang->load("rights");
			$this->set_message(lang("rights_no_rights"));
			$uiTable="";
		}

		/**
		 * output
		 */
		$this->_show_type("form");
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
			$this->lang->load("update_delete");
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
