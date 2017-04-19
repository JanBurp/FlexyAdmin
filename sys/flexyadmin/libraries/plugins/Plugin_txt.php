<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** \ingroup plugins
 * 
 * - Als in een txt_.. veld een link zit, wordt gekeken of deze bekend is in tbl_links:
 *  - Zo nee, dan wordt eerst gekeken of deze link op een andere lijkt.
 *  - Er wordt gevraagd of de nieuwe link moet worden toegevoegd, vervangen door een gelijkende, of niets.
 *
 * @author Jan den Besten
 * @internal
 */

class Plugin_txt extends Plugin {

	public function __construct() {
		parent::__construct();
    $this->CI->load->library('form_validation');
	}
	
	public function _after_update() {
    // Zijn er links in de txt?
    $txt_fields = filter_by_key($this->newData,'txt_');
    $links = array();
    foreach ($txt_fields as $field => $txt) {
      if (preg_match_all('/<a[^>]*(href=\"([^\"]*)\")[^>]*>(.*)<\/a>/uU', $txt,$matches)) {
        // trace_($matches);
        // Maak er een array van: title=>url met unieke links
        foreach ($matches[2] as $key => $url) {
          $links[$url] = array(
            'field'     => $field,
            'title'     => $matches[3][$key],
            'url'       => $url,
            'prep_url'  => $this->CI->form_validation->prep_url_mail($url),
            'href'      => $matches[1][$key]
          );
        }
      }
    }
    
    // Vervang links door hun prepped variant & Controleer of de links bestaan in tbl_links en pas de links array aan
    $prepped = 0;
    if (count($links)>0) {
      $this->CI->data->table('tbl_links');
      foreach ($links as $key => $link) {
        // Prep
        if ($link['url']!==$link['prep_url']) {
          $this->newData[$link['field']] = str_replace( $link['href'], 'href="'.$link['prep_url'].'"', $this->newData[$link['field']]);
          $prepped++;
        }
        // Een nieuwe?
        if ($this->CI->data->select('str_title,url_url')->where('url_url',$link['prep_url'])->get_row()) {
          unset($links[$key]);
        }
      }
    }
    
    // Zijn er nog nieuwe links? Voeg ze toe
    if (count($links)>0) {
      $this->CI->lang->load('help');
      $this->add_message( '<h3>'.langp('new_links_found',count($links)).'</h3>' );
      
      $this->CI->data->table('tbl_links');
      $this->add_message( '<ul>' );
      foreach ($links as $key => $link) {
        $this->add_message( '<li><a href="'.$link['prep_url'].'" target="_blank">'.$link['title'].' - '.$link['prep_url'].'</li>' );

        $this->CI->data->insert_new_link(array(
          'str_title' => $link['title'],
          'url_url'   => $link['prep_url'],
        ), $this->newData);

      }
      $this->add_message( '</li>' );
    }
    
		return $this->newData;
	}
	

}

?>