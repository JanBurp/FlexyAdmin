<?php 
/**
 * Uitbreiding op <a href="http://codeigniter.com/user_guide/helpers/text_helper.html" target="_blank">Text_helper van CodeIgniter</a>.
 *
 * $Author$
 * $Date$
 * $Revision$
 * 
 * @author: Jan den Besten
 * @copyright: (c) Jan den Besten
 * @file
 */


 /**
  * Wordt gebruikt door highlight_code_if_needed()
  *
  * @param string $matches 
  * @return void
  * @author Jan den Besten
  * @ignore
  */
 function _callback_highlight($matches) {
	$code=$matches[1];
	$code=strip_tags($code,'<p><br><a><div><b><strong><em><italic>');
  // $code=str_replace(' ',"\t",$code);
	$code=str_replace(array('&lt;','&gt;'),array('<','>'),$code);
  $code=str_replace('&amp;','&',$code);
	$code=str_replace(array('<br/>','<br />','<br>'),"\n",$code);
  $code=highlight_code($code);
  $code=str_replace("\n",'',$code);
  $code = preg_replace("/\<br \/>(?!.*\<br \/>)/uU", "", $code);
  $code = preg_replace("/&nbsp;((?!.*&nbsp;).*\<\/code>\z)/uU", "$1", $code);
	return $code;
}

/**
 * Wordt gebruikt door highlight_code_if_needed()
 *
 * @param string $matches 
 * @return void
 * @author Jan den Besten
 * @ignore
 */

function _callback_highlight_span($matches) {
  $code=_callback_highlight($matches);
  $code=str_replace(array('<code>','</code>'),array('<span class="code">','</span>'),$code);
  $code=preg_replace('/\s*?<\/span>/','</span>',$code);
  $code=preg_replace('/&nbsp;<\/span><\/span>/','</span></span>',$code);
	return $code;
}


/**
 * Kleurt tekst binnen HTML code tags (&lt;code&gt;)
 * 
 * Maakt gebruik van CodeIgniter's highlight_code():
 * 
 * - werkt alleen binnen de code tags.
 * - haalt eerst (eventueel eerder geplaatst) kleurcodes weg en schoont alles binnen de code tags nog wat verder op (trimmen etc).
 * 
 * @param string $txt mee te geven HTML
 * @return string
 * @author Jan den Besten
 */

function highlight_code_if_needed($html) {
  $html=preg_replace_callback('/<code>(.*?)<\/code>/usm','_callback_highlight',$html);
  $html=preg_replace_callback('/<span class="code">(.*?)<\/span>/usm','_callback_highlight_span',$html);
	return $html;
}


?>