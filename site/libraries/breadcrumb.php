<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Maakt een breadcrumb
 * 
 * Bestanden
 * ----------------
 *
 * - site/config/breadcrumb.php - Hier kun je wat instellingen doen
 * - site/views/breadcrumb.php - De view van de breadcrumb, hier kun je de html aanpassen
 *
 * Installatie
 * ----------------
 *
 * - Voeg ergens in een view (bv views/site.php) de code `<div id="breadcrumb"><?=$modules['breadcrumb']?></div>` toe
 * - Laad de module altijd in: `$config['autoload_modules']=array('breadcrumb');`
 * 
 * 
 * @package default
 * @author Jan den Besten
 */

class Breadcrumb extends Module {

    /**
    * @ignore
    */
    public function __construct() {
      parent::__construct();
    }


    /**
     * @ignore
     */
    public function index($page) {
      // Haal elementen op van de uri
      $segments=$this->CI->uri->segment_array();
      $links=$segments;
      $segments=array_reverse($segments);
      $segments=array_combine($segments,$segments);
      foreach ($segments as $uri => $url) {
        $segments[$uri]=array(
                              'uri'   =>  site_url(implode('/',$links)),
                              'class' =>  '',
                              'last'  =>  false
                            );
        array_pop($links);
      }
      // Voeg home toe
      if ($this->config('include_home')) {
        $home=$this->CI->menu->get_home();
        $segments[$home['uri']]=array('uri'=>site_url(),'name'=>$home['name'],'class'=>'breadcrumb_home','last'=>false);
      }
      $segments=array_reverse($segments);

      // Haal tekst op uit menu
      foreach ($segments as $uri => $segment) {
        if (!isset($segment['name'])) {
          $this->CI->db->select($this->config('title_field'));
          $this->CI->db->where_uri($uri);
          $item=$this->CI->db->get_row(get_menu_table());
          $segments[$uri]['name']=$item[$this->config('title_field')];
        }
      }
      
      // Stel einde in
      end($segments);
      $segments[key($segments)]['last']=true;
      
      return $this->CI->view('breadcrumb',array('segments'=>$segments),true);
    }
 

}

/* End of file breadcrumb.php */
/* Location: ./application/libraries/breadcrumb.php */
