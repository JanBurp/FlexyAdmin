<?php 

/**
 * Update 3480:
 * - Alle _thumbcache bestanden zonder encode assets map
 * - Alle plugins naar eigen submap binnen libraries
 * 
 * @author Jan den Besten
 * $Revision$
 * @copyright (c) Jan den Besten
 */

class Update_3480 extends Model_updates {
  
  public	function __construct() {
    parent::__construct();
  }
  
  public function update() {
    // Verwijder alle _thumbcache bestanden, ze worden vanzelf wel weer aangemaakt bij het tonen
    empty_map( $this->config->item('THUMBCACHE') );
    $this->_add_message('Removed all thumbs in <b><i>'.$this->config->item('THUMBCACHE').'</i><b>','glyphicon-ok btn-success');
    
    // Verplaats alle plugins naar submap libraries/plugins
    
    
    return parent::update();
  }

 }
?>
