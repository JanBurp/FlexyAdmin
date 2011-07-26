<?

class Links extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function main($item) {
		$item['view']='links';
		$links=$this->db->get_results('tbl_links');
		return array('view'=>'links','links'=>$links);
	}

}

?>