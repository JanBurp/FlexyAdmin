<?
// Minimized versions of Javascript & CSS files (see http://refresh-sf.com/yui/)
$minimize=FALSE;
$minimize=TRUE;

if ($minimize) {
	$js='.min.js';
	$css='.min.css';
}
else{
	$js='.js';
	$css='.css';
}
$isGrid=(has_string('grid',$show_type) or has_string('filemanager',$show_type) or has_string('stats',$show_type));
$isForm=has_string('form',$show_type);
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>FlexyAdmin <?=$title?></title>
	<meta http-equiv="content-type"	content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<base href="<?=base_url()?>" />
	
	<link rel="shortcut icon" href="<?=admin_assets()?>img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main<?=$css?>" type="text/css" />
	<link rel="stylesheet" href="site/assets/css/admin.css" type="text/css" />
	
	<!--[if lte IE 6]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie6.css);</style><![endif]-->
	<!--[if IE 7]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie8.css);</style><![endif]-->

	<!-- JS variables -->
	<script language="javascript" type="text/javascript">
	<!--
	config=new Object;
	config.site_url="<?=site_url()?>/";
	<?
	if (isset($jsVars) && !empty($jsVars)) {
		foreach ($jsVars as $key => $value) {
			?>config.<?=$key?>="<?=$value?>";<?
		}
	}
	?>
	
	-->
	</script>

	<!-- jQuery -->
	<script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.4.3.min.js"></script>
	<!-- jQuery UI -->
	<link rel="stylesheet" type="text/css" href="sys/jquery/ui/custom-theme/jquery-ui-1.8.7.custom<?=$css?>" />
	<script language="javascript" type="text/javascript" src="sys/jquery/ui/jquery-ui-1.8.7.custom.min.js"></script>
	<!-- jQuery plugins-->
	<link rel="stylesheet" type="text/css" href="sys/jquery/plugins/fullsize/fullsize.css" />
	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/fullsize/jquery.fullsize<?=$js?>"></script>
	
	<? if ($isGrid): ?>
		<!-- grid Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/filterable/jquery.filterable<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/tablesorter/jquery.tablesorter.min.js"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/cvi_text_lib<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/jquery.flipv<?=$js?>"></script>
	<? endif; ?>
	<? if ($isForm): ?>
		<!-- form Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/ui/i18n/ui.datepicker-nl.js"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/timepicker/jquery.ui.timepicker<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/form/jquery.form<?=$js?>"></script>
		<? if ($show_editor): ?>
			<!-- editor Scripts -->
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
			<script>
			$().ready(function() {
			   $('textarea.htmleditor').tinymce({
						document_base_url : "<?=base_url()?>",
						plugins : "paste,advimage,media,table,inlinepopups,embed,fullscreen,preview",
						plugin_preview_width : "<?=$preview_width?>",
						plugin_preview_height : "<?=$preview_height?>",
				 		dialog_type : "modal",
						inlinepopups_skin : "flexyadmin",
						language : "<?=$language?>",
						docs_language : "<?=$language?>",
						theme : "advanced",
						skin : "flexyadmin",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location: "bottom",
						theme_advanced_resizing : true,
						theme_advanced_resize_horizontal : false,
						content_css : "<?=assets()?>css/text.css",
						<? if (isset($formats)): ?>
						theme_advanced_blockformats : "<?=$formats?>",
						<? else: ?>
						theme_advanced_blockformats : "h1,h2,h3",
						<? endif; ?>
						theme_advanced_styles : "<?=$styles;?>",
						extended_valid_elements : "iframe[align<bottom?left?middle?right?top|class|frameborder|height|id|longdesc|marginheight|marginwidth|name|scrolling<auto?no?yes|src|style|title|width]",
						external_image_list_url : "<?=assets()?>/lists/img_list.js?"+new Date().getTime(),
						media_external_list_url : "<?=assets()?>/lists/media_list.js?"+new Date().getTime(),
						external_embed_list_url : "<?=assets()?>/lists/embed_list.js?"+new Date().getTime(),
						external_link_list_url : "<?=assets()?>lists/link_list.js?"+new Date().getTime(),
						relative_urls : true,
						theme_advanced_buttons1 : "<?=$buttons1?>",
						theme_advanced_buttons2 : "<?=$buttons2?>",
						theme_advanced_buttons3 : "<?=$buttons3?>"
			   });
			});
			</script>
		<? endif; ?>
	<? endif; ?>
	<!-- FlexyAdmin Scripts -->
	<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/jFlexyCore<?=$js?>"></script>
	<? if ($isGrid): ?>
		<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/jFlexyGrid<?=$js?>"></script>
	<? endif; ?>
	<? if ($isForm): ?>
		<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/jFlexyForm<?=$js?>"></script>
	<? endif; ?>
	<script language="javascript" type="text/javascript" src="site/assets/js/admin.js"></script>
</head>

<body>
<div id="header">
	<a id="flexyadmin" href="<?=api_url('API_home');?>"><span class="hide">FlexyAdmin - HOME</span></a>
</div>

