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
   
   private $import_table;
   
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
      $this->import_table = $table;
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
      $set_data = array();
      foreach ($data as $key=>$row) {
        $set = array();
        foreach ($row as $field => $value) {
          $value=trim($value);
          if ($value) {
            $type = get_prefix($field);
            switch ($type) {

              case 'id' :
                $value = $this->_add_foreign($field,$value);
                $set[remove_suffix($field,'.')] = $value;
                break;
              
              // case 'rel':
              //   $this->_add_many_to_many($field,$value);
              //   break;

              case 'txt':
                $head = get_suffix($field,':');
                $field = get_prefix($field,':');
                if ($head) {
                  $value = '<b>'.$head.':&nbsp;</b>&nbsp;'.$value.'<br>';
                }
                if (!isset($set[$field])) $set[$field]='';
                $set[$field] .= $value;
                break;

              case 'str':
                $set[$field] = $value;
                break;
            }
          }
        }
        $id = '-';
        if ($set) {
          // Check eerst of item al bestaat
          $this->CI->data->table($this->import_table)->where($set);
          $exists = $this->CI->data->get_row();
          if ($exists) {
            $id = $exists['id'];
          }
          else {
            $this->CI->data->set($set);
            $id = $this->CI->data->insert();
          }
        }
        $row = array_unshift_assoc($row,'id',$id);
        $data[$key]=$row;
      }
      
      
      
      // Show Data
      array_unshift($fields,'id');
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
  
  
  /**
   * Voeg foreign data toe en geef id terug.
   * Checkt eerst of foreign data al bestaat, zoja, geeft ook de id terug.
   *
   * @param string $field 
   * @param mixed $value 
   * @return int $id
   * @author Jan den Besten
   */
  private function _add_foreign($field,$value) {
    $id = FALSE;
    
    // Check de many_to_one relaties
    $foreign_field = get_suffix($field,'.');
    $field = remove_suffix($field,'.');
    $this->CI->data->table( $this->import_table );
    $relation = $this->CI->data->get_setting(array('relations','many_to_one',$field));
    
    // Als relatie bestaat, ga verder
    if ($relation) {
      $other_table = $relation['other_table'];
      // Ok table & set known
      $this->CI->data->table($other_table)->where($foreign_field,$value);
      $exists = $this->CI->data->get_row();
      if ($exists) {
        $id = $exists['id'];
      }
      else {
        $this->CI->data->set($foreign_field,$value);
        $id = $this->CI->data->insert();
      }
    }
    return $id;
  }

}

?>