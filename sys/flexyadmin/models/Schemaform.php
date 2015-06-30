<?php

/** \ingroup models
 * 
 * Maakt een schemaform van gegeven datarow
 * Zie http://schemaform.io
 *
 * @author: Jan den Besten
 * $Revision$
 * @copyright: (c) Jan den Besten
 */

class Schemaform extends CI_Model {
  
  var $default  = array(
    'schema' => array(
      'type'       => 'object',
      'title'      => 'row',
      'properties' => array(),
      'required'   => array(),
    ),
    'form'  => array(),
  );
  
  // var $validations=array(
  //   => 'minLength',
  //   => 'maxLength',
  //
  // );
  // http://json-schema.org/latest/json-schema-validation.html
  //    5.1.  Validation keywords for numeric instances (number and integer)
  //        multipleOf, maximum, exclusiveMaximum, minimum, exclusiveMinimum
  //    5.2.  Validation keywords for strings
  //        maxLength, minLength, pattern
  //    5.3.  Validation keywords for arrays
  //        additionalItems, items, maxItems, minItems,uniqueItems
  //    5.4.  Validation keywords for objects
  //        maxProperties, minProperties, required, additionalProperties, properties, patternProperties, dependencies
  //    5.5.  Validation keywords for any instance type
  //        enum, type, allOf, anyOf, oneOf, not, definitions

  
  /**
   */
  public function __construct() {
    parent::__construct();
    $this->load->model('ui');
  }
  
  
  /**
   * Creert schemaform van een standaard row uit de database
   *
   * @param string $row 
   * @param string $name 
   * @return array $schemaform
   * @author Jan den Besten
   */
  public function create_from_row($row,$name='row') {
    $sf=$this->default;
    $sf['schema']['title'] = $name;
    
    $cfgDefault=$this->config->item('FIELDS_default');
    $cfgPrefix=$this->config->item('FIELDS_prefix');
    $cfgSpecial=$this->config->item('FIELDS_special');
    
    foreach ($row as $name => $value) {
      // default
      $schemaType = $cfgDefault['schemaType'];
      $formType   = $cfgDefault['formType'];
      $validation = $cfgDefault['validation'];
      
      // from prefix
      $prefix = get_prefix($name);
      if (el($prefix,$cfgPrefix)) {
        $schemaType = (el(array($prefix,'schemaType'),$cfgPrefix,$schemaType));
        $formType   = (el(array($prefix,'formType'),$cfgPrefix,$formType));
        $validation = (el(array($prefix,'validation'),$cfgPrefix,$validation));
      }
      // special
      if (el($name,$cfgSpecial)) {
        $schemaType = (el(array($name,'schemaType'),$cfgSpecial,$schemaType));
        $formType   = (el(array($name,'formType'),$cfgSpecial,$formType));
        $validation = (el(array($name,'validation'),$cfgSpecial,$validation));
      }

      // Put in Schema
      $sf['schema']['properties'][$name]=array(
        'title' => $this->ui->get($name),
        'type'  => $schemaType,
      );
      // validation
      $validation=explode('|',$validation);
      // required
      if ($key=array_search('required',$validation)) {
        unset($validation['$key']);
        $sf['schema']['required'][]=$name;
      }
      $sf['schema']['properties'][$name]['validation']=$validation;
      
      // Put in form (for keeping order)
      $sf['form'][] = array(
        'key'   => $name,
        'type'  => $formType,
      );
      
      // TODO: bovenstaande ook uit db: zoe flexy_field->type()
      // TODO: FIELDSETS
      
      
    }
    
    trace_($sf['schema']);
    trace_($sf['form']);
    return $sf;
  }
  
}
