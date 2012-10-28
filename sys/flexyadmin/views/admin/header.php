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



<!DOCTYPE html>
<html>
<head>
	<title>FlexyAdmin <?=$title?></title>
	<meta http-equiv="content-type"	content="text/html;charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<base href="<?=base_url()?>" />
	
	<link rel="shortcut icon" href="<?=admin_assets()?>img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?=admin_assets()?>css/admin_main<?=$css?>" type="text/css" />
	<link rel="stylesheet" href="<?=assets()?>css/admin.css" type="text/css" />
	
	<!--[if lte IE 7]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie7.css);</style><![endif]-->
	<!--[if IE 8]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie8.css);</style><![endif]-->
	<!--[if IE 9]><style type="text/css" media="screen">@import url(<?=admin_assets()?>css/ie9.css);</style><![endif]-->

	<!-- JS variables -->
	<script language="javascript" type="text/javascript">
	<!--
	var config = new Object;
	config.site_url = "<?=site_url()?>/";
	config.form_nice_dropdowns = "<?=$this->config->item('FORM_NICE_DROPDOWNS')?>";
	<?
	if (isset($jsVars) && !empty($jsVars)) {
		foreach ($jsVars as $key => $value) {
			?>config.<?=$key?>="<?=$value?>";<?
		}
	}
	?>
	
	-->
	</script>

	<!-- jQuery NB 1.8.2 gaat niet in combi met tinyMCE 3.5.7 (styledropdown) -->
	<script language="javascript" type="text/javascript" src="sys/jquery/jquery-1.7.2.min.js"></script>
	<!-- jQuery UI -->
	<link rel="stylesheet" type="text/css" href="sys/jquery/ui/custom-theme/jquery-ui-1.8.7.custom<?=$css?>" />
	<script language="javascript" type="text/javascript" src="sys/jquery/ui/jquery-ui-1.8.23.custom.min.js"></script>
  <script src="sys/jquery/ui/jquery.ui.progressbar.min.js" type="text/javascript" charset="utf-8"></script>
  
	<!-- jQuery plugins-->
	<link rel="stylesheet" type="text/css" href="sys/jquery/plugins/fullsize/fullsize.css" />
	<script language="javascript" type="text/javascript" src="sys/jquery/plugins/fullsize/jquery.fullsize<?=$js?>"></script>
	
	<? if ($isGrid): ?>
		<!-- grid Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/filterable/jquery.filterable<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/tablesorter/jquery.tablesorter.min.js"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/cvi_text_lib<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/flipv/jquery.flipv<?=$js?>"></script>
    
    <!-- Load plupload  -->
    <script src="sys/jquery/plugins/plupload/js/plupload.full.js" type="text/javascript" charset="utf-8"></script>
    <script src="sys/jquery/plugins/plupload/js/jquery.ui.plupload/jquery.ui.plupload.js" type="text/javascript" charset="utf-8"></script>
    <? if ($language!='en'): ?><script src="sys/jquery/plugins/plupload/js/i18n/<?=$language?>.js" type="text/javascript" charset="utf-8"></script><? endif ?>
    <link rel="stylesheet" href="sys/jquery/plugins/plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css" type="text/css" media="screen" title="no title" charset="utf-8" />
    
	<? endif; ?>
	<? if ($isForm): ?>
		<!-- form Scripts -->
		<script language="javascript" type="text/javascript" src="sys/jquery/ui/i18n/ui.datepicker-nl.js"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/timepicker/jquery.ui.timepicker<?=$js?>"></script>
		<script language="javascript" type="text/javascript" src="sys/jquery/plugins/multiselect/jquery.multiselect<?=$js?>"></script>
		<link rel="stylesheet" href="sys/jquery/plugins/multiselect/jquery.multiselect.css" type="text/css" media="screen" title="no title" charset="utf-8" />
		<script src="sys/jquery/plugins/colorpicker/js/colorpicker.js" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" href="sys/jquery/plugins/colorpicker/css/colorpicker.css" type="text/css" media="screen" title="no title" charset="utf-8" />
		<? if ($show_editor): ?>
			<!-- editor Scripts -->
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script language="javascript" type="text/javascript" src="sys/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
			<script>
			$(document).ready(function() {
			   $('textarea.htmleditor').tinymce( {
						document_base_url : "<?=base_url()?>",
						plugins: "paste,advimage,media,table,inlinepopups,fullscreen,preview,embed",
						plugin_preview_width : "<?=$preview_width?>",
						plugin_preview_height : "<?=$preview_height?>",
						dialog_type : "modal",
						inlinepopups_skin : "flexyadmin",
						language : "<?=$language?>",
						docs_language : "<?=$language?>",
						theme : "advanced",
            skin : "flexyadmin",
            theme_advanced_font_sizes : "9px,10px,11px,12px,14px,16px,20px,24px,32px",
						<?
						switch ($editor_class) {
							case 'wide':
								echo 'width:"608",';
								break;
							case 'high':
								echo 'height:"450",';
								break;
							case 'big':
								echo 'width:"608",height:"450",';
								break;
						}
						?>
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
						entities : '160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,'
						        + '171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,'
						        + '183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,'
						        + '194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,'
						        + '205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,'
						        + '216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,'
						        + '227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,'
						        + '238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,'
						        + '249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,350,#350,351,#351,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,'
						        + '917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,'
						        + '931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,'
						        + '950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,'
						        + '963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,'
						        + '8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,'
						        + '8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,'
						        + '8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,'
						        + '8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,'
						        + '8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,'
						        + '8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,338,OElig,339,oelig,352,Scaron,353,scaron,'
						        + '376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,'
						        + '8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro',
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
	<script language="javascript" type="text/javascript" src="<?=assets()?>js/admin.js"></script>
</head>

<body>
<div id="header">
	<a id="flexyadmin" href="<?=api_url('API_home');?>"><span class="hide">FlexyAdmin - HOME</span></a>
</div>

