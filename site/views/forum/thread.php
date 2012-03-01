<div class="forum_thread">
  <h2><?=$thread['str_title']?></h2>
  
  <? foreach ($messages as $id_message => $message): ?>
    <div class="forum_message">
      <div class="text"><?=$message['txt_text']?></div>
      <? if (!empty($message['media_file'])): ?><div class="forum_message_file"><?=lang('message_file')?><a href="file/<?=$config['attachment_folder'].'/'.$message['media_file']?>"><?=$message['media_file']?></a></div><? endif ?>
      <div class="forum_message_info">
        <a href="mailto:<?=$message['cfg_users__email_email']?>"><?=$message['cfg_users__str_username']?></a><span class="seperator">|</span>
        <?=strftime( $config['datetime_format'] ,mysql_to_unix($message['tme_date']))?>
        <!-- <span class="right"><? if (isset($config['user_name'])): ?><a href="<?=site_url($config['uri_reply'])?>"><?=lang('menu_answer')?></a><? endif;?></span> -->
      </div>
    </div>
  <? endforeach ?>

  <? if (empty($form)): ?>
  <div class="forum_tools"><? if (isset($config['user_name'])): ?><a href="<?=site_url($config['uri_reply'])?>"><?=lang('menu_answer')?></a><? endif;?></div>
  <? endif ?>
</div>

<? if (!empty($pagination)): ?>
<div class="forum_pagination"><?=$pagination?></div>
<? endif ?>

<? if (!empty($form)): ?>
  <div class="forum_form forum_reply">
    <? if ($config['use_tinymce']): ?>
    	<!-- editor Scripts -->
    	<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    	<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
    	<script>
    	$(document).ready(function() {
    	   $('textarea.txt_text').tinymce( {
    				document_base_url : "<?=base_url()?>",
    				plugins: "paste,embed",
    				dialog_type : "modal",
    				inlinepopups_skin : "flexyadmin",
    				language : "<?=$this->site['language']?>",
    				docs_language : "<?=$this->site['language']?>",
    				theme : "advanced",
    				skin : "default",
    				theme_advanced_toolbar_location : "top",
    				theme_advanced_toolbar_align : "left",
    				theme_advanced_statusbar_location: "bottom",
    				theme_advanced_resizing : true,
    				theme_advanced_resize_horizontal : false,
    				content_css : "<?=assets()?>css/text.css",
    				extended_valid_elements : "iframe[align<bottom?left?middle?right?top|class|frameborder|height|id|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style|title|width]",
    				relative_urls : true,
            theme_advanced_buttons1 : "cut,copy,pastetext,pasteword,selectall,undo,bold,italic,bullist,numlist,removeformat,link,unlink,embed",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : ""
    	   });
    	});
    	</script>
    <? endif ?>
    <?=$form?>
  </div>
<? endif ?>
