<?

// THIS IS EXPERIMENTAL, DON'T USE THIS FOR NOW


class Model_example extends Model {

	function Model_example() {
		parent::Model();
	}

	function main($item) {
		$this->site['content'].='<h2>MODEL EXAMPLE</h2>';
		return $item;
	}

}

?>