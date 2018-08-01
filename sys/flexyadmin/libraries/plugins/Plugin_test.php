<?php 

class Plugin_test extends Plugin {
	
	public function __construct()	{
		parent::__construct();
    $this->CI->load->library('table');
    $this->CI->load->library('flexy_auth');
    
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
    $this->CI->table->set_template($template);
	}
  
  public function _admin_api() {
    if (!IS_LOCALHOST) return;
    $args = func_get_args();
    if ($args) {
      $method = array_shift($args);
      if ($method) {
        $method = current($method);
        return $this->$method(current($args));
      }
    }

    $this->CI->data->table('tbl_menu')
                   ->set(array(
                      'str_title' => 'TEST PAGINA',
                      'txt_text'  => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat.',
                    ))
                   ->insert();
    $this->add_trace( $this->CI->data->get_query_info() );
    $this->add_trace_sql( $this->CI->data->last_query() );
    
    return $this->content;
  }
  
  
  private function settings() {
    if (!IS_LOCALHOST) return;

    $tables = $this->CI->data->list_tables();
    $tables = filter_by($tables,'tbl');
    $tables = array('res_assets');
    foreach ($tables as $table) {
      $this->CI->data->table( $table );
      echo h($table);
      echo h('grid_set',3);
      trace_( $this->CI->data->get_setting('grid_set') );
      echo h('form_set',3);
      trace_( $this->CI->data->get_setting('form_set'));
      echo hr();
    }

    // return $this->content;
  }


  private function validations() {
    if (!IS_LOCALHOST) return;
    $this->CI->load->library('form_validation');
    
    $tables = $this->CI->data->list_tables();
    $tables = filter_by($tables,'tbl');
    foreach ($tables as $table) {
      echo h($table);
      $fields = $this->CI->db->list_fields( $table );
      // $fields = array_unset_keys($fields,array('id','order','uri','self_parent'));
      foreach ($fields as $field) {
        echo h($field,2);
        $validations = $this->CI->form_validation->get_rules($table,$field);
        trace_($validations);
      }
    }
    
  }




  private function options() {
    if (!IS_LOCALHOST) return;
    
    $tables = $this->CI->data->list_tables();
    foreach ($tables as $table) {
      $this->CI->data->table( $table );
      $options_settings = $this->CI->data->get_setting('options');
      if ($options_settings) {
        echo h($table);
        trace_( $this->CI->data->get_setting('options') );
        trace_( $this->CI->data->get_options());
      }
    }
    
  }

  
  
  private function users( $user_id=FALSE ) {
    if (!IS_LOCALHOST) return;

    $this->CI->load->library('flexy_auth');
    if ($user_id) {
      $user = $this->CI->flexy_auth->get_user($user_id);
      $users[$user_id] = $user;
    }
    else {
      $users = $this->CI->flexy_auth->get_users();
    }

    foreach ($users as $id=>$user) {
      $user['groups.description']='';
      foreach ( $user['groups'] as $group ) {
        $user['groups.description'] = add_string( $user['groups.description'], $group['description'],' | ');
      }
      $user['rights.description']='<table>';
      foreach ($user['rights']['items'] as $item=>$rights) {
        $user['rights.description'] .= '<tr><td><b>'.$item.'<b></td><td>'.$this->CI->flexy_auth->rights_to_string( $rights ). '</td></tr>';
      }
      $user['rights.description'] .= '</table>';
      $users[$id] = array_keep_keys( $user, array('id','str_username','groups.description','rights.description'));
    }

    echo( '<h1>User rights</h1>' );
    $this->CI->table->set_heading( array('id','user_name','groups.description','rights') );
    foreach ($users as $user) {
      $this->CI->table->add_row( $user );
    }
    echo $this->CI->table->generate();
  }
  
  
  
  private function relations() {
    if (!IS_LOCALHOST) return;
    
    // many_to_one
    $this->CI->data->table( 'tbl_kinderen' );
    $many_to_one['without']  = "";
    $many_to_one['normal']   = "->with( 'many_to_one' )";
    $many_to_one['specific'] = "->with( 'many_to_one', ['id_adressen'=>['str_zipcode','str_city']] )";
    $many_to_one['abstract'] = "->with( 'many_to_one', ['id_adressen'=>'abstract'] )";
    $many_to_one['json']  = "->with_json( 'many_to_one', ['id_adressen'] )";
    $many_to_one['flat']     = "->with_flat_many_to_one( ['id_adressen'] )";
    $this->eval_table('many_to_one',$many_to_one);

    // one_to_many
    $this->CI->data->table( 'tbl_adressen' );
    $one_to_many['without']  = "";
    $one_to_many['normal']   = "->with( 'one_to_many' )";
    $one_to_many['specific'] = "->with( 'one_to_many', ['tbl_kinderen'=>['str_first_name','str_last_name']] )";
    $one_to_many['abstract'] = "->with( 'one_to_many', ['tbl_kinderen'=>'abstract'] )";
    $one_to_many['json']  = "->with_json( 'one_to_many', ['tbl_kinderen'] )";
    $this->eval_table('one_to_many',$one_to_many);

    // many_to_many
    $this->CI->data->table( 'tbl_groepen' );
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
      $eval = 'return $this->CI->data'.$value.'->get( 2 );';
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
      $eval = 'return $this->CI->data'.$value.'->get_result( 2 );';
      $array = eval($eval);
      $num_rows = $this->CI->data->num_rows();
      $result[$key] = 'num_rows = '.$num_rows.' (2)'.br().highlight_code(array2php( array_slice($array,0,2) ));
      // Query
      $sql[$key] = highlight_code(nice_sql( $this->CI->data->last_query() ));
    }
    array_unshift($with,"\n<strong>with()</strong>");
    array_unshift($get,"<strong>get()->result_array()</strong>");
    array_unshift($result,"<strong>get_result()</strong>");
    array_unshift($sql,"\n<strong>->last_query()</strong>");
    
    echo( '<h1>'.$caption.'</h1>' );
    $this->CI->table->set_heading( array_keys($with) );
    $this->CI->table->add_row( $with );
    $this->CI->table->add_row( $sql );
    $this->CI->table->add_row( $get );
    $this->CI->table->add_row( $result );
    echo $this->CI->table->generate();
  }
  
  

  private function menu() {
    $menu = $this->CI->data->table('tbl_menu')
                        ->unselect('txt_text')
                        ->tree('full_uri','uri')
                        ->where_tree( 'full_uri','een_pagina/subpagina')
                        ->get_result();
    trace_($menu);
    trace_($this->CI->data->get_query_info());
  }
  

}

?>
