<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
	* Import vanuit CSV
	* 
	* Eerste regel bevat de veldnaam:
	* - Kan een standaard veldnaam zijn
	* - Bij een foreign_key wordt er een item in de foreign_table aangemaakt (als die niet bestaat)
	* - Bij een rel_table idem
	* - Bij txt_ velden kunnen meerdere kolommen worden samengevoegd
	*
	* @author Jan den Besten
	*/
 class Plugin_import extends Plugin {
   
   private $template = array(
                        'table_open' => '<table class="table table-bordered table-sm table-responsive">',
                        'thead_open' => '<thead class="thead-default">',
                      );
   
   
   public function __construct() {
     parent::__construct();
     $this->CI->load->library('table');
     $this->CI->table->set_template($this->template);
   }


	public function _admin_api($args=NULL) {
		if (!$this->CI->flexy_auth->is_super_admin()) return;
    
    // Tabel & Bestand kiezen
    $table = $this->CI->input->post('table');
    
    if ($table and !empty($_FILES)) {
      $this->add_message(h($table));
      
      $file = current($_FILES);
      $csv = file_get_contents( $file['tmp_name'] );
      
      $lines = explode(PHP_EOL,$csv);
      
      $fields = array_shift($lines);
      $fields = explode(';',$fields);

      // Create Data
      $data = array();
      foreach ($lines as $line) {
        $line = explode(';',$line);
        $line = array_combine($fields,$line);
        $data[] = $line;
      }
      
      // Import Data
      foreach ($data as $row) {
        foreach ($row as $field => $value) {
          $type = get_prefix($field);
          switch ($type) {
            case 'str':
              
              break;
          }
        }
      }
      
      
      // Show Data
      $this->CI->table->set_heading($fields);
      $this->add_message( $this->CI->table->generate($data) );
      
      
    }
    else {
			$tables=$this->CI->data->list_tables();
  		$tableOptions = array();
  		foreach ($tables as $key=>$table) {
  			if ($this->config('use_ui_names'))
  				$tableOptions[$table]=$this->CI->lang->ui($table);
  			else
  				$tableOptions[$table]=$table;
  		}

  		$formData = array("table"	=> array("validation"=>"required",'type'=>'dropdown','options'=>$tableOptions),
  										  "file"	=> array("validation"=>"required",'type'=>'file') );

  		$this->CI->load->library('form');
  		$form=new form($this->CI->uri->get());
      $form->set_framework('bootstrap');
  		$form->set_data($formData);
			$this->add_message( $form->render() );
    }
    
    
    return $this->show_messages();
	}

}

?>