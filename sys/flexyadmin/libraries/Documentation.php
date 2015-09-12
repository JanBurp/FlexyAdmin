<?php

/**
 * Parsed documentatie van een php bestand.
 *
 * @package default
 * @author Jan den Besten
 */

class Documentation {
  
  /**
   * Geeft gehele documentatie terug
   *
   * @param string $file 
   * @return array( 'short'=>'', 'full'=>'' )
   * @author Jan den Besten
   */
  public function get($file) {
    $doc = FALSE;
    if (file_exists($file)) {
      $text = file_get_contents($file);
      if ( preg_match( "/\\\*\*(.*)\*\//uiUsx", $text, $match) ) {
        $txt = $match[1];
        // cleanup a bit
        $txt = preg_replace("/^\s?\*/um", "", $txt);
        $txt = preg_replace("/^\s?/um", "", $txt);
        $txt = preg_replace("/^\\\ingroup.*\n/um", "", $txt);
        // Split
        $lines = explode( PHP_EOL, $txt );
        $long = $lines;
        $params = array();
        foreach ($lines as $key => $line) {
          if ( in_array( substr($line,0,1), array('@','$') ) ) {
            unset($long[$key]);
            if (preg_match("/[@$](.*):\s?(.*)/um", $line,$match)) {
              $params[$match[1]] = $match[2];
            }
          }
        }
        // 
        $doc = array(
          'name'  => remove_suffix( get_suffix( $file,'/' ), '.'),
          'short' => $lines[0],
          'long'  => implode(PHP_EOL,$long),
          'full'  => $txt,
          'params'=> $params,
        );
        $doc['html_long'] = str_replace(PHP_EOL,'<br>',htmlentities($doc['long']));
      }
    }
    return $doc;
  }
  
}