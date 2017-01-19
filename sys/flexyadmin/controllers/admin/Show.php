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
    $this->config->load('schemaform');
    $this->load->model('ui');
	}

	public function index() {
		$this->view_admin( 'admin_404' );
	}

	/**
	 * This controls the grid view
	 *
	 * @param string $name name of datamodel
	 */
	public function grid($name='',$path='') {
    // Options for grid result
    $default = array(
      'limit'   => 10,
      'offset'  => 0,
      'order'   => '',
      'filter'  => ''
    );
    $options = object2array(json_decode($this->input->get('options')));
    $options = array_merge($default,$options);
    
    // Api
    $this->data->table($name);
    if ($path) $name=$path;
    $api = 'table';
    
    // Default order
    $options['order'] = $this->_order($options['order']);
    
    // Show grid
    $grid = array(
      'title'    => $this->ui->get($name),
      'name'     => $name,
      'api'      => $api,
      'order'    => $options['order'],
      'offset'   => $options['offset'],
      'limit'    => $options['limit'],
      'filter'   => $options['filter'],
      'type'     => ($path)?'media':'table',
    );
	  $this->view_admin( 'vue/grid', $grid );
	}
  
	/**
	 * This controls the media view
	 *
	 * @param string $name name of path
	 */
  public function media( $path ) {
    $this->grid('res_assets',$path);
  }

  /**
   * Geeft standaard order
   *
   * @param string $order 
   * @return void
   * @author Jan den Besten
   */
  private function _order($order) {
    if (empty($order)) {
      $order = $this->data->get_setting('order_by');
      if (!empty($order)) {
        $order = explode(',',$order)[0];
        $order = explode(' ',$order);
        if (isset($order[1]) and strtoupper($order[1])==='DESC') {
          $order = '_'.$order[0];
        }
        else {
          $order = $order[0];
        }
      }
    }
    return $order;
  }
  
/**
 * This controls the form view
 *
 * @param string 	$table 	Table name
 * @param mixed 	$id 		id
 */
	public function form() {
    $args = func_get_args();
    $name = array_shift($args);
    $id = array_shift($args);
      
    // Data
    $this->data->table($name);
    $api = 'row';
    
    // Show form
    $form = array(
      'name'      => $name,
      'title'     => $this->ui->get($name),
      'id'        => $id,
      'api'       => $api
    );
	  $this->view_admin( 'vue/form', $form );
	}
  

}

?>
