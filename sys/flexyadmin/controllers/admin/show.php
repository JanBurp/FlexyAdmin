<?
require_once(APPPATH."core/AdminController.php");

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
	// var $grid_set;	// array that contains current table/offset/order/search

	function __construct() {
		parent::__construct();
		$this->load->model("flexy_field","ff");
	}

	function index() {
		$this->_show_all();
	}


	
	private function _save_grid_set($set=array()) {
		$default=array('table'=>'','offset'=>'','order'=>'','search'=>'');
		$set=array_merge($default,$set);
		$this->session->set_userdata('grid_set',$set);
	}
	
	private function _open_grid_set() {
		$set=$this->grid_set=$this->session->userdata('grid_set');
		return $set;
	}
	
	private function _open_grid_set_uri() {
		$set=$this->_open_grid_set();
		$uri=api_uri('API_view_grid',$set['table']);
		unset($set['table']);
		foreach ($set as $key => $value) {
			if (!empty($value)) $uri.="/$key/$value";
		}
		return $uri;
	}
	
	private function _reset_grid_set() {
		$this->session->unset_userdata('grid_set');
	}
	
	

/**
 * This controls the order of a table
 *
 * @param string $table Table name
 * @param int $id maybe an id, the last that changed
 * @param mixed $newOrder (top|bottom|up|down|(number))
 */

	function order($table="",$id="",$newOrder="") {
		if (!empty($table) and ($id!="") and !empty($newOrder) and $this->user->has_rights($table,$id)>=RIGHTS_EDIT) {
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
			$sub=el('sub',$args);
			$offset=el('offset',$args,0);
			$order=el('order',$args);
			$search=el('search',$args);
			$this->_save_grid_set(array('table'=>$table,'offset'=>$offset,'order'=>$order,'search'=>$search));
			// strace_($args);

			if (!empty($table) and $this->db->table_exists($table)) {
				$singleRow=( $this->cfg->get('CFG_table',$table,"int_max_rows") == 1);
				if ($singleRow) {
					$this->db->select("id");
					$row=$this->db->get_row($table,1);
					$id=$row["id"];
					$this->form_args['form']=$table.':'.$id;
					$this->form();
					return;
				}
				else {
					if ($right=$this->user->has_rights($table,$id)) {
						$restrictedToUser=$this->user->restricted_id($table);
						$this->load->library("pagination");
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
							if (!empty($where)) {
								$this->db->where($where,NULL,FALSE);
							}
						}
						$uiTable=$this->ui->get($table);

						// get information (from db) needed later...
						$hasField=array();
						$hasField['self_parent']=$this->db->has_field($table,"self_parent");
						$hasField['user']=$this->db->has_field($table,"user");

						$pagination=$this->cfg->get("CFG_table",$table,'b_pagination');
						if ($pagination) $pagination=$this->cfg->get('cfg_configurations','int_pagination');
						
						// How to order?
						if ($hasField['self_parent']) {
							$this->db->order_as_tree();
						}
						elseif ($order) {
							$orderArr=explode(':',$order);
							foreach ($orderArr as $key => $ord) {
								if (substr($ord,0,1)=='_') $ord=substr($ord,1).' DESC';
								$ordPre=get_prefix($ord);
								if ($ord!='') {
									if ($ordPre=='id' and $ord!='id')
										$this->db->order_by_foreign($ord);
									elseif ($ordPre=='rel')
										$this->db->order_by_many($ord);
									else
										$this->db->order_by($ord);
								}
							}
						}
						
						// has rights?
						if ($restrictedToUser>0 and $hasField['user']) {
							$this->db->where($table.".user",$restrictedToUser);
							$this->db->dont_select("user");
						}
						if ($table=="cfg_users") $this->db->where('cfg_users.id >=',$this->user_id);
							
						$this->db->add_foreigns_as_abstracts();
						if (isset($tableInfo['b_grid_add_many']) and $tableInfo['b_grid_add_many']) $this->db->add_many();
						$this->db->max_text_len(250);
						
						// search?
						if ($search) {
							$fields=$this->db->list_fields($table);	
							$searchArr=array();
							foreach ($fields as $field) {
								$searchArr[]=array('field'=>$field,'search'=>$search,'or'=>'OR','table'=>$table);
							}
							// search in many_tables if any
							if ($this->db->many) {
								if ($this->db->many) {
									if (!is_array($this->db->many))
										$many_tables=$this->db->get_many_tables($table);
									else
										$many_tables=$many;
								}
								foreach ($many_tables as $many_table => $value) {
									$searchArr[]=array('field'=>$many_table,'search'=>$search,'or'=>'OR','table'=>$table);
								}
							}
							$this->db->search($searchArr);
						}


						$data=$this->db->get_result($table,$pagination,$offset);
						$total_rows=$this->db->last_num_rows_no_limit();

						$last_order=$this->db->get_last_order();
						if (substr($last_order,0,1)!='(') $order=$last_order;

						if (empty($data) and empty($search)) {
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

							$grid=new grid();

							if ($pagination) {
								$base_url=api_url('API_view_grid',$table);
								$pagination=array('base_url'=>$base_url,'per_page'=>$pagination,'total_rows'=>$total_rows,'offset'=>$offset);
								$grid->set_pagination($pagination);
							}

							// if (empty($id) and !empty($data)) {
							// 	$id=current($data);
							// 	$id=$id['id'];
							// }


							/**
							 * if data: first render data, then put data in grid and render as html
							 */
							if ($right<RIGHTS_EDIT) {
								// remove order fields
								foreach ($data as $id => $row) unset($data[$id]['order']);
							}
							$data=$this->ff->render_grid($table,$data,$right,$info);

							if (empty($uiTable)) $uiTable=$this->ui->get($table);
							$tableHelp=$this->ui->get_help($table);
							if (!empty($tableHelp)) {
								$uiShowTable=help($uiTable." ",$tableHelp);
							}
							else
								$uiShowTable=$uiTable;
							$grid->set_data($data,$uiShowTable);
							$grid->set_order($order);
							$grid->set_search($search);
							$keys=array();
							if (!empty($data)) {
								$keys=array_keys(current($data));
								$keys=array_combine($keys,$keys);
							}
							if ($right>=RIGHTS_ADD) {
								$newUri=api_uri('API_view_form',$table.':-1');
								if (!empty($info)) $newUri.='/info/'.$info;
								$newIcon=anchor($newUri,help(icon("new"),langp('grid_new',$uiTable)) );
								if ($this->cfg->get('CFG_table',$table,'int_max_rows')<count($data))
									$grid->prepend_to_captions($newIcon,"new");
								else
									$grid->prepend_to_captions('&nbsp;');
							}
							$grid->set_headings($this->ui->get($keys,$table));
							if ($right>=RIGHTS_DELETE)
								$grid->set_heading(PRIMARY_KEY,help(icon("select all"),lang('grid_select_all')).help(icon("delete"),lang('grid_delete'), array("class"=>"delete") ) );
							else
								$grid->set_heading(PRIMARY_KEY,'');
							
							if (!empty($id)) $grid->set_current($id);
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
				and $right=$this->user->has_rights($table,$id)) {
			$restrictedToUser=$this->user->restricted_id($table);
			
			$this->load->library('form_validation');
			$this->load->library('upload');
			$this->load->model("order");
			$this->load->helper('html');
			$this->lang->load("form");

			$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
			$this->load->library("form");

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
				$data[PRIMARY_KEY]="-1";
			}
			else {
				if ($restrictedToUser>0 and $this->db->has_field($table,"user")) {
					$this->db->where("user",$restrictedToUser);
					$this->db->dont_select("user");
				}
				if ($id!="") {
					$this->db->where($table.".".PRIMARY_KEY,$id);
				}
				$data=$this->db->get_result($table);
				// trace_('#show#'.$this->db->last_query());
				// strace_($data);
				$options=el("options",$data);
				$multiOptions=el("multi_options",$data);
				$data=current($data);
			}
			// strace_($options);
			// strace_($multiOptions);

			/**
			 * if data: first render data for the form class, then put data in form
			 */
			if (!empty($data)) {
				$this->ff->set_restricted_to_user($restrictedToUser,$this->user_id);
				$ffData=$this->ff->render_form($table,$data,$options,$multiOptions);
				
				$actionUri=api_uri('API_view_form',$table.':'.$id);
				if (!empty($info)) $actionUri.='/info/'.$info;
				$form=new form($actionUri);

				$uiTable=$this->ui->get($table);
				$tableHelp=$this->ui->get_help($table);
				if (!empty($tableHelp)) {
					$uiShowTable=help($uiTable,$tableHelp);
				}
				else
					$uiShowTable=$uiTable;

				$form->set_data($ffData,$uiShowTable);
				$form->add_password_match();
				$form->hash_passwords();

				/**
				 * Validate form, if succes, update/insert data
				 */
				if ($form->validation()) {
					$this->lang->load("update_delete");
					$this->load->model('queu');

					$newData=$form->get_data();
					$newData=$this->_after_update($table,$data,$newData);

					$this->crud->table($table,$restrictedToUser);
					if ($id==-1) {
						$id=$this->crud->insert(array('data'=>$newData));
						$this->set_message(langp("insert_new",$table));
					}
					else {
						$id=$this->crud->update(array('where'=>array(PRIMARY_KEY=>$id), 'data'=>$newData));
						$this->set_message(langp("update_succes",$table));
					}
					
					// Make calls that plugins might have put in the queu
					$this->queu->run_calls();
					// Remove all cached files
					delete_all_cache();

					$redirectUri=$this->_open_grid_set_uri();
					if (!empty($info)) $redirectUri.='/info/'.$info;
					
					if ( $id===FALSE ) {
						$this->set_message(langp("update_error",$table));
						redirect($redirectUri);
					}
					else {
						$this->load->model("login_log");
						$this->login_log->update($table);
						redirect($redirectUri.'/current/'.$id);
					}
				}

				/**
				 * Validate form, no succes: show form, maybe with validation errors
				 */
				else {
					$this->_add_content(validation_errors());

					$keys=array_keys($ffData);
					$keys=array_combine($keys,$keys);
					$uiFieldNames=array();
					foreach($keys as $key) {
						$fieldHelp=$this->ui->get_help($table.".".$key);
						if (!empty($fieldHelp))
							$uiFieldNames[$key]=help($this->ui->get($key,$table),$fieldHelp);
						else
							$uiFieldNames[$key]=$this->ui->get($key,$table);
					}
					$form->set_labels($uiFieldNames);
					
					// Fieldsets?
					$fieldsets=$this->cfg->get('cfg_table_info',$table,'str_fieldsets');
					if (empty($fieldsets)) $fieldsets=array();
					elseif (is_string($fieldsets)) $fieldsets=explode(',',$fieldsets);
					// add default fieldset with name of table
					array_unshift($fieldsets,$this->ui->get($table));
					$form->set_fieldsets($fieldsets);
					
					if ($right<RIGHTS_EDIT) $form->no_submit();
					$html=$form->render("html ".$table,$table);
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
		$userTable='cfg_users';
		$userId=$this->session->userdata("user_id");
		$this->db->select("id,str_username,email_email,gpw_password,str_language");
		$this->db->add_options();
		$this->db->where("id",$userId);
		$userData=$this->db->get_result($userTable);
		$options=el("options",$userData);
		$userData=current($userData);
		
		/**
		 * Init user form
		 */
		$title=ucwords($userData["str_username"]);
		$fieldset=$title;
		
		$formData=$this->ff->render_form($userTable,$userData,$options);
		foreach ($formData as $key => $value) {
			$formData[$key]['fieldset']=$title;
		}

		$this->lang->load("update_delete");
		$this->lang->load("form");

		$this->load->library('form_validation');
		$this->load->helper('html');
		$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
		$this->load->library("form");
		$form=new form(api_uri('API_user'));
		$form->set_data($formData,$userData["str_username"]);
		$form->set_fieldsets($title);
		$form->add_password_match();
		$form->hash_passwords();
		$form->set_caption($title);
		/**
		 * Validate form, if succes, make form do an update
		 */
		if ($form->validation()) {
			$this->load->model('queu');
			
			$newData=$form->get_data();
			if (empty($newData['gpw_password'])) unset($newData['gpw_password']);
			
			$newData=$this->_after_update($userTable,'',$newData);
			$resultId=$this->crud->table($userTable)->update(array('where'=>array(PRIMARY_KEY=>$userId), 'data'=>$newData));
			
			$this->queu->run_calls();
			
			if ($resultId===FALSE) {
				$this->set_message(langp("update_error",$userTable,$resultId));
			}
			else {
				$this->set_message(lang("update_user_changed"));
				$this->load->model("login_log");
				$this->login_log->update($userTable);
			}
			redirect(api_uri('API_home'));
		}
		else {
			/**
			 * Render
			 */
			$html=$form->render("html");
			$this->_add_content(validation_errors());
			$this->_add_content($html);
			$this->_show_type("form");
		}
		$this->_show_all();
	}


}

?>
