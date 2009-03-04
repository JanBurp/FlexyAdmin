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
		$this->load->model("flexy_data","fd");
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
		if ($this->has_rights($table,$id)) {
			/**
			 * re-order data
			 */
			if (!empty($table) and !empty($id) and !empty($newOrder)) {
				$this->load->model("order");
				$this->order->reorder($table,$id,$newOrder);
				$this->set_message("Re-ordered");
				$this->load->model("login_log");
				$this->login_log->update($table);
				redirect(api_uri('API_view_grid',$table,$id));
			}
		}
		else {
			$this->set_message("Sorry, you don't have rights to do this.");
			$uiTable="";
			/**
			 * show
			 */
			$this->_show_all($uiTable);
		}
	}


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
			if ($this->has_rights($table,$id)) {
				$this->load->model("grid");

				/**
				 * get data
				 */
				$this->fd->foreign_with_abstracts();
				$this->fd->joins(true);
				$data=$this->fd->get_results($table);
				if (empty($data)) {
					/**
					 * if no data, start an input form
					 */
					 $this->form($table,-1);
					 return;
				}
				else
				{
					/**
					 * if data: first render data, then put data in grid and render as html
					 */
					$data=$this->ff->render_grid($table,$data);

					$grid=new grid();
					$uiTable=$this->uiNames->get($table);
					$grid->set_data($data,$uiTable);
					$keys=array_keys(current($data));
					$keys=combine($keys,$keys);
					$newIcon=anchor(api_uri('API_view_form',$table,-1),icon("new"));
					$grid->prepend_to_caption($newIcon,"new");
					$grid->set_headings($this->uiNames->get($keys,$table));
					$grid->set_heading(pk(),"Edit");
					if (!empty($id)) {
						$grid->set_current($id);
					}
					$html=$grid->render("html",$table,"grid");
					$this->_set_content($html);
				}
			}
			else {
				$this->set_message("Sorry, you don't have rights to do this.");
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
		if ($this->has_rights($table,$id)) {
			$this->load->library('form_validation');
			$this->load->library('upload');
			$this->load->helper('html');

			$this->form_validation->set_error_delimiters('<div id="formmessage">', '</div>');
			$this->load->model("form");
			/**
			 * get data
			 */
			$this->fd->joins(true);
			$this->fd->abstracts(true);
			$this->fd->with_options(true);
			if ($id!="") $this->fd->where($table.".".pk(),$id);
			$data=$this->fd->get_results($table);
			$options=el("options",$data);
			$multiOptions=el("multi_options",$data);
			$data=current($data);

			/**
			 * if no data, new item: fill data with default values
			 */
			if (empty($data)) {
				$this->fd->joins(true);
				$this->fd->foreign(true);
				$this->fd->with_options(true);
				$data=$this->fd->defaults($table);
				$options=el("options",$data);
				$multiOptions=el("multi_options",$data);
				$data=current($data);
				$data[pk()]="-1";
				$id=-1;
			}

			/**
			 * if data: first render data for the form class, then put data in form
			 */

			$data=$this->ff->render_form($table,$data,$options,$multiOptions);

			$form=new form(api_uri('API_view_form',$table,$id));
			$uiTable=$this->uiNames->get($table);
			$form->set_data($data,$uiTable);

			/**
			 * Validate form, if succes, make form do an update
			 */
			if ($form->validation()) {
				$resultId=$form->update($table);
				if (is_string($resultId)) {
					$this->set_message("Update/Insert error on '$table','$resultId'");
					redirect(api_uri('API_view_grid',$table));
				}
				else {
					if ($id==-1)
						$this->set_message("New item inserted on '$table'");
					else
						$this->set_message("Updated item from '$table'");
					$this->load->model("login_log");
					$this->login_log->update($table);
					redirect(api_uri('API_view_grid',$table,$resultId));
				}
			}
			/**
			 * Validate form, no succes: show form, maybe with validation errors
			 */
			else {
				$this->_add_content(validation_errors());

				$keys=array_keys($data);
				$keys=combine($keys,$keys);
				$form->set_labels($this->uiNames->get($keys,$table));
				$html=$form->render("html",$table);
				if ($form->has_htmlfield()) $this->use_editor();
				$this->_add_content($html);
			}
		}
		else {
			$this->set_message("Sorry, you don't have rights to do this.");
			$uiTable="";
		}

		/**
		 * output
		 */
		$this->_show_type("form");
		$this->_show_all($uiTable);
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
			$this->form_validation->set_message('valid_rgb', 'Wrong color code in the %s field');
			return FALSE;
		}
		$rgb=strtoupper($rgb);
		if (ctype_xdigit($rgb))
			return "#$rgb";
		else {
			$this->form_validation->set_message('valid_rgb', 'Wrong color code in the %s field');
			return FALSE;
		}
	}






}

?>
