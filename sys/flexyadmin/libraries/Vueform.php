<?php 
/** \ingroup libraries
 * VueForm
 * @author Jan den Besten
 */
class Vueform {

	private $CI;
  private $form_id;
  private $settings = array();
  private $default = array(
    // 'form_id' => '',
    'action'  => '',
    'name'    => '',
    'title'   => '',
    'fields'  => array(),
    // 'buttons' => array(),
  );
  private $data = null;

	public function __construct( $settings=array() ) {
		$this->CI = &get_instance();
    $this->CI->lang->load('form');
		$this->CI->load->library('form_validation');
		$this->initialize($settings);
	}
  

  /**
   * Initialiseer het formulier
   *
   * @param array $settings
   * @return $this
   * @author Jan den Besten
   */
	public function initialize( $settings = array()  ) {
    if (!is_array($settings)) $settings = array('action'=>$settings);
    $this->settings = array_merge($this->default,$settings);
    // defaults
    if (empty($this->settings['action'])) $this->settings['action'] = $this->CI->uri->uri_string();
    if (empty($this->settings['title']))  $this->settings['title']  = $this->settings['name'];
    return $this;
	}
  
  
  public function render() {
    // Vul values aan als die al bestaan
    $data = $this->get_data();
    foreach ($data as $field => $value) {
      if (isset($this->settings['fields'][$field])) {
        $this->settings['fields'][$field]['value'] = $value;
      }
    }
    return $this->CI->load->view('admin/vue/form', $this->settings, true);
  }

  
  public function validation() {
    $data = $this->get_data();
    foreach ($this->settings['fields'] as $field => $info) {
      if (isset($info['validation'])) {
        $this->CI->form_validation->set_rules( $field, el('label',$info,$field), $info['validation'] );
      }
    }
    $validated = $this->CI->form_validation->run();
    if (!$validated) {
      $errors = $this->validation_errors();
      foreach ($errors as $field => $error) {
        $this->settings['fields'][$field]['validation_error'] = $error;
      }
    }
    return $validated;
  }
  
  public function validation_errors() {
    return $this->CI->form_validation->get_error_messages();
  }
  
  public function get_data() {
    $this->data = $this->CI->input->post();
    // array?
    foreach ($this->data as $field => $value) {
      if ( el('multiple',$this->settings['fields'][$field]) ) {
        if ($value) {
          $value = explode(',',$value);
        }
        $this->data[$field] = $value;
      }
    }
    return $this->data;
  }


}

?>
