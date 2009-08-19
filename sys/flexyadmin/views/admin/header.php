<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html>
<head>
	<title>FlexyAdmin 2009</title>
	<meta http-equiv="content-type"	content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<base href="<?=base_url()?>" />

	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main.css" type="text/css" />
	<!--[if lte IE 6]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie6.css);</style><![endif]-->
	<!--[if IE 7]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie8.css);</style><![endif]-->

	<link rel="stylesheet" type="text/css" href="sys/jquery/ui/theme/ui.all.css" />

	<script language="javascript" type="text/javascript">
	<!--
	config=new Object;
	config.site_url="<?=site_url()?>/";
	<?
	if (isset($js)) {
		foreach ($js as $key => $value) {
			?>config.<?=$key?>="<?=$value?>";<?
		}
	}
	?>
	
	-->
	</script>

	<script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.3.2.min.js"></script>
	<script language="javascript" type="text/javascript" src="sys/jquery/ui/jquery-ui-1.7.1.custom.min.js"></script>

	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/filterable/jquery.filterable.js"></script>
	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/tablesorter/jquery.tablesorter.min.js"></script>

	<? if ($show_type=="form"): ?>
	<!-- js for form -->
	<script language="javascript" type="text/javascript" src="sys/jquery/ui/i18n/ui.datepicker-nl.js"></script>
	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/form/jquery.form.js"></script>
	<? endif; ?>

	<script language="javascript" type="text/javascript" src="<?=admin_assets()?>js/flexyadmin.js"></script>


	<? if ($show_type=="form" and $show_editor): ?>
	<!-- js for form, html editor -->
	<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
	<script type="text/javascript">
	tinyMCE_GZ.init({
		plugins : "paste,table,fullscreen,advimage,media",
		themes : 'advanced',
		languages : '<?=$language?>',
		disk_cache : true,
		debug : false
	});
	</script>

	<script language="javascript" type="text/javascript">
	tinyMCE.init({
			language : "<?=$language?>",
			docs_language : "<?=$language?>",
			mode : "specific_textareas",
			editor_selector : "htmleditor",
			theme : "advanced",
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
			plugins : "paste,table,fullscreen,advimage,media",
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
	</script>
	<? endif; ?>

</head>

<body>

<div id="header">
	<a id="flexyadmin" href="<?=api_url('API_home');?>"><span class="hide">FlexyAdmin - HOME</span></a>
</div>

