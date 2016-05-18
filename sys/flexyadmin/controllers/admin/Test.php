<?php require_once(APPPATH."core/AdminController.php");

class Test extends AdminController {
	
	public function __construct()	{
		parent::__construct();
    $this->load->library('table');
    
    $template = array(
      'table_open'            => '<table border="1" cellpadding="4" cellspacing="0">',
      'thead_open'            => '<thead>',
      'thead_close'           => '</thead>',
      'heading_row_start'     => '<tr>',
      'heading_row_end'       => '</tr>',
      'heading_cell_start'    => '<th align="left">',
      'heading_cell_end'      => '</th>',
      'tbody_open'            => '<tbody>',
      'tbody_close'           => '</tbody>',
      'row_start'             => '<tr>',
      'row_end'               => '</tr>',
      'cell_start'            => '<td valign="top" align="left"><pre><code>',
      'cell_end'              => '</code></pre></td>',
      'row_alt_start'         => '<tr>',
      'row_alt_end'           => '</tr>',
      'cell_alt_start'        => '<td valign="top" align="left"><pre><code>',
      'cell_alt_end'          => '</code></pre></td>',
      'table_close'           => '</table>'
    );
    $this->table->set_template($template);
	}
  
  public function index() {
    
    $this->data->table( 'cfg_users' )->select('id,str_username')->with( 'many_to_one', array( 'id_user_group' => array('str_name','str_description') ) );
    $users = $this->data->get_result();
    $tables = $this->db->list_tables();
    
    foreach ($users as $id => $user) {
      foreach ($tables as $table) {
        $users[$id]['rights'][$table] = $this->user->rights_to_string( $this->user->has_rights( $table, '', 0, $id) );
      }
    }

    trace_( $users );
    
    
    
  }
  
  
  public function relations() {
    if (!$this->user->is_super_admin()) return;
    
    // many_to_one
    $this->data->table( 'tbl_leerlingen' );
    $many_to_one['without']  = "";
    $many_to_one['normal']   = "->with( 'many_to_one' )";
    $many_to_one['specific'] = "->with( 'many_to_one', ['id_adressen'=>['str_zipcode','str_city']] )";
    $many_to_one['abstract'] = "->with( 'many_to_one', ['id_adressen'=>'abstract'] )";
    $many_to_one['json']  = "->with_json( 'many_to_one', ['id_adressen'] )";
    $many_to_one['flat']     = "->with_flat_many_to_one( ['id_adressen'] )";
    $this->eval_table('many_to_one',$many_to_one);

    // one_to_many
    $this->data->table( 'tbl_adressen' );
    $one_to_many['without']  = "";
    $one_to_many['normal']   = "->with( 'one_to_many' )";
    $one_to_many['specific'] = "->with( 'one_to_many', ['tbl_leerlingen'=>['str_first_name','str_last_name']] )";
    $one_to_many['abstract'] = "->with( 'one_to_many', ['tbl_leerlingen'=>'abstract'] )";
    $one_to_many['json']  = "->with_json( 'one_to_many', ['tbl_leerlingen'] )";
    $this->eval_table('one_to_many',$one_to_many);

    // many_to_many
    $this->data->table( 'tbl_groepen' );
    $many_to_many['without']  = "";
    $many_to_many['normal']   = "->with( 'many_to_many' )";
    $many_to_many['specific'] = "->with( 'many_to_many', ['rel_groepen__adressen'=>['str_zipcode']] )";
    $many_to_many['abstract'] = "->with( 'many_to_many', ['rel_groepen__adressen'=>'abstract'] )";
    $many_to_many['json']  = "->with_json( 'many_to_many', ['rel_groepen__adressen'] )";
    $this->eval_table('many_to_many',$many_to_many);
    
  }
  
  
  private function eval_table( $caption, $with ) {

    $get=array();
    $result=array();
    $sql=array();
    
    foreach ($with as $key => $value) {
      // With
      $with[$key] = highlight_code( str_replace(')->',")\n->",$value) );

      // Get
      $eval = 'return $this->data'.$value.'->get( 2 );';
      $query = eval($eval);
      if ($query) {
        $num_rows = $query->num_rows();
        $array = array_slice( $query->result_array(),0,2 );
        $get[$key] = 'num_rows = '.$num_rows.' (2)'.br().highlight_code(array2php( $array ));
      }
      else {
        $get[$key] = span(array('style'=>'color:#F00;')).'Database ERROR:<br>'.$this->db->error()['message']._span();
      }
      
      // Result
      $eval = 'return $this->data'.$value.'->get_result( 2 );';
      $array = eval($eval);
      $num_rows = $this->data->num_rows();
      $result[$key] = 'num_rows = '.$num_rows.' (2)'.br().highlight_code(array2php( array_slice($array,0,2) ));
      // Query
      $sql[$key] = highlight_code(nice_sql( $this->data->last_query() ));
    }
    array_unshift($with,"\n<strong>with()</strong>");
    array_unshift($get,"<strong>get()->result_array()</strong>");
    array_unshift($result,"<strong>get_result()</strong>");
    array_unshift($sql,"\n<strong>->last_query()</strong>");
    
    echo( '<h1>'.$caption.'</h1>' );
    $this->table->set_heading( array_keys($with) );
    $this->table->add_row( $with );
    $this->table->add_row( $sql );
    $this->table->add_row( $get );
    $this->table->add_row( $result );
    echo $this->table->generate();
  }
  

}

?>
