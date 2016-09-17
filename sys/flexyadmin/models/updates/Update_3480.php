<?php 

/**
 * Update 3480:
 * - Alle _thumbcache bestanden zonder encode assets map
 * - Alle plugins naar eigen submap binnen libraries
 * 
 * @author Jan den Besten
 * @copyright (c) Jan den Besten
 */

class Update_3480 extends Model_updates {
  
  public	function __construct() {
    parent::__construct();
  }
  
  public function update() {
    // Verwijder alle _thumbcache bestanden, ze worden vanzelf wel weer aangemaakt bij het tonen
    empty_map( $this->config->item('THUMBCACHE') );
    $this->_add_message('Removed all thumbs in <b><i>'.$this->config->item('THUMBCACHE').'</i></b>','glyphicon-ok btn-success');
    
    // Verplaats alle plugins naar submap libraries/plugins
    $folders=array('sys/flexyadmin/libraries','sys/flexyadmin/config','site/libraries','site/config');
    foreach ($folders as $folder) {
      $plugin_folder = $folder.'/plugins';
      if (!file_exists($plugin_folder)) {
        mkdir($plugin_folder);
        $this->_add_message('Created <strong><em>'.$plugin_folder.'</em></strong>.','glyphicon-ok btn-success');
      }
    }
    
    foreach ($folders as $folder) {
      $plugins = scan_map( $folder, $types='php' );
      foreach ($plugins as $key => $plugin) {
        $plugins[$key] = str_replace($folder.'/','',$plugin);
      }
      if ( has_string('config',$folder) )
        $plugins = filter_by($plugins,'plugin');
      else
        $plugins = filter_by($plugins,'Plugin');
      //
      foreach ($plugins as $plugin) {
        $old = $folder.'/'.$plugin;
        $new = $folder.'/plugins/'.$plugin;
        if (rename($old,$new))
          $this->_add_message('Moved <strong><em>'.$plugin.'</em></strong> => <strong><em>'.$new.'</em></strong>.','glyphicon-ok btn-success');
        else
          $this->_add_message('ERROR when moving <strong><em>'.$plugin.'</em></strong> => <strong><em>'.$new.'</em></strong>.','glyphicon-error btn-danger');
      }
    }
    
    
    return parent::update();
  }

 }
?>
