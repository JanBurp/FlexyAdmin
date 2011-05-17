<?

class Links extends Model {

	function Links() {
		parent::Model();
	}

	function main($item) {
		$links=$this->db->get_results('tbl_links');
		return array('view'=>'links','links'=>$links);
	}

}

?>