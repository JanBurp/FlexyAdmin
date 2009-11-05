<?
// Minimized versions of Javascript & CSS files (see http://refresh-sf.com/yui/)
$minimize=FALSE;
// $minimize=TRUE;
if ($minimize) {
	$js='.min.js';
	$css='.min.css';
}
else{
	$js='.js';
	$css='.css';
}

$show_type=trim($show_type);
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>FlexyAdmin 2009</title>
	<meta http-equiv="content-type"	content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<base href="<?=base_url()?>" />

	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main<?=$css?>" type="text/css" />
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
	<script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.3.2.min.js"></script>
	<!-- jQuery UI -->
	<link rel="stylesheet" type="text/css" href="sys/jquery/ui/theme/ui.all.css" />
	<script language="javascript" type="text/javascript" src="sys/jquery/ui/jquery-ui-1.7.2.custom.min.js"></script>
	<!-- jQuery plugins-->
	<link rel="stylesheet" type="text/css" href="sys/jquery/plugins/fullsize/fullsize.css" />
	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/fullsize/jquery.fullsize<?=$js?>"></script>
	
	<? if ($show_type=="grid" or $show_type=="filemanager list" or $show_type=="filemanager icons" or $show_type=="grid graph stats"): ?>
		<!-- grid Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/filterable/jquery.filterable<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/tablesorter/jquery.tablesorter.min.js"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/cvi_text_lib<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/jquery.flipv<?=$js?>"></script>
	<? endif; ?>
	<? if ($show_type=="form"): ?>
		<!-- form Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/ui/i18n/ui.datepicker-nl<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/timepicker/timepicker<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/form/jquery.form<?=$js?>"></script>
		<? if ($show_editor): ?>
			<!-- editor Scripts -->
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
			<script>
			$().ready(function() {
			   $('textarea.htmleditor').tinymce({
						plugins : "paste,advimage,media,table,inlinepopups",
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
						<? if (isset($formats)): ?>
						theme_advanced_blockformats : "<?=$formats?>",
						<? else: ?>
						theme_advanced_blockformats : "h1,h2,h3",
						<? endif; ?>
						theme_advanced_styles : "<?=$styles;?>",
						external_image_list_url : "<?=assets()?>/lists/img_list.js",
						media_external_list_url : "<?=assets()?>/lists/media_list.js",
						relative_urls : true,
						document_base_url : "<?=base_url()?>",
						content_css : "<?=assets()?>css/text.css",
						external_link_list_url : "<?=assets()?>lists/link_list.js",
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
	<? if ($show_type=="grid" or $show_type=="filemanager list" or $show_type=="filemanager icons"): ?>
		<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/jFlexyGrid<?=$js?>"></script>
	<? endif; ?>
	<? if ($show_type=="form"): ?>
		<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/jFlexyForm<?=$js?>"></script>
	<? endif; ?>

</head>

<body>
<div id="header">
	<a id="flexyadmin" href="<?=api_url('API_home');?>"><span class="hide">FlexyAdmin - HOME</span></a>
</div>

