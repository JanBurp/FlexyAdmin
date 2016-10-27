<?php require_once(APPPATH."core/AdminController.php");

/** \ingroup controllers
 * This Controller shows a grid or form
 *
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Show extends AdminController {
	
	public function __construct() {
		parent::__construct();
	}

	public function index() {
		$this->view_admin( 'admin_404' );
	}


	/**
	 * This controls the grid view
	 *
	 * @param string $name name of datamodel
	 */
	public function grid( $name='' ) {
    $this->data->table($name);
    $result = $this->data->select_txt_abstract()->get_grid();
    $first  = current($result);
    $grid = array(
      'title'   => $this->ui->get($name),
      'headers' => $this->ui->get($this->data->get_setting(array('grid_set','fields'))),
      'data'    => $result,
      'info'    => $this->data->get_query_info(),
    );
	  $this->view_admin( 'grid', $grid );
	}


/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form( $table='',$id=false ) {
	}


}

?>
