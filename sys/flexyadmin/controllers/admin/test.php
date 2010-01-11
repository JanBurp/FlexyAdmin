<?
require_once(APPPATH."controllers/admin/MY_Controller.php");


class Test extends AdminController {

	function Test() {
		parent::AdminController();
	}

	function index() {
		$this->grid();
	}
	
	function grid() {
		$params=$this->uri->uri_to_assoc(4);
		$page=$params['page'];
		$order=$params['order'];

		
		// $this->load->model('flexyHtml','Html');
		// $this->Html->set_title('Title');
		// $this->Html->set_data(array('Paragraaf 1'=>'Bladiebla hjhd hj agkfgh k jhg hghg.','Paragraaf 2'=>'En nog veel meer.'));
		// $this->_add_content($this->Html->view());

		// $this->load->model('flexyTable','Table');
		// $this->Table->set_title('Table');
		// $this->Table->set_data($this->db->get_result('tbl_links'));
		// $this->_add_content($this->Table->view());

		$this->load->model('flexyGrid','Grid');
		$this->Grid->set_title('Grid');
		$this->Grid->set_order($order);
		$this->Grid->set_pagination_length(10);
		$this->Grid->set_pagination_url('admin/test/grid');
		$this->Grid->set_pagination_page($page);
		$this->db->order_by(trim($order,'_'),substr($order,0,1)=='_' ? 'DESC':'ASC');
		$data=$this->db->get_result('log_login');
		$this->Grid->set_data($data);
		$this->_add_content($this->Grid->view());


		$this->_show_all();
	}

}

?>
