<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
	
	
	
	/**
	 * Here are some own form validation callback functions
	 * Routings are set so that admin/show/valid_* is routed to admin/show, so these callbacks are not reached by url
	 */

		function valid_rgb($rgb) {
			$rgb=trim($rgb);
			if (empty($rgb)) {
				return TRUE;
			}
			$rgb=str_replace("#","",$rgb);
			$len=strlen($rgb);
			if ($len!=3 and $len!=6) {
				$this->lang->load("form_validation");
				$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
				return FALSE;
			}
			$rgb=strtoupper($rgb);
			if (ctype_xdigit($rgb))
				return "#$rgb";
			else {
				$this->lang->load("form_validation");
				$this->form_validation->set_message('valid_rgb', lang('valid_rgb'));
				return FALSE;
			}
		}
	
	
	
}
?>
