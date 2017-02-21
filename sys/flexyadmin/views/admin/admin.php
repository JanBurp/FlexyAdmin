<!DOCTYPE html>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin - <?=$str_title?></title>
	<base href="<?=base_url()?>" />

  <link rel="stylesheet" href="<?=admin_assets()?>css/font-awesome.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="<?=admin_assets()?>dist/flexyadmin.css" type="text/css" media="screen">
</head>

<body>

<div id="main" class="container-fluid">

  <progress class="progress" v-show="state.progress > 0" :value="state.progress" max="100"></progress>
  <flexy-messages v-show="state.messages.length > 0" :messages="state.messages"></flexy-messages>
  <flexy-modal :options="state.modal"></flexy-modal>
  
  <div id="header" class="navbar navbar-fixed-top flex-row d-flex justify-content-between">
    <div class="navbar-brand navbar-collapse">
      <a href="<?=$base_url?>" title="FlexyAdmin <?=$build?>"><span class="flexy-block btn btn-secondary">
        <span class="fa fa-home fa-lg"></span>
      </span></a>
      <flexy-blocks v-once href="<?=$base_url?>" text="<?=$str_title?>" class="hidden-md-down"/>
    </div>
    <div class="navbar-nav">
      <?=$headermenu?>
    </div>
  </div>
  
  <div id="row" class="row">
    <div id="flexy-menu-side" class="col-sm-2"><?=$sidemenu?></div>
    <div id="content" class="col-sm-10"><?=$content?></div>
  </div>
  
  <div id="mask" v-cloak v-show="state.progress>0">
    <span class="spinner fa fa-spinner fa-pulse fa-fw"></span>
  </div>
  
</div>



<script type="text/javascript" charset="utf-8">
var _flexy = {
  'auth_token'    : '<?=$user['auth_token']?>',
  'media_view'    : '<?=$user['str_filemanager_view']?>',
  'language'      : '<?=$language?>',
  'language_keys' : JSON.parse('<?=addslashes(array2json($lang_keys))?>'),
  'tinymceOptions': '<?=array2json($tinymceOptions)?>',
};
</script>
<script src='<?=admin_assets()?>js/tinymce/tinymce.min.js' type="text/javascript" charset="utf-8"></script>
<script src="<?=admin_assets()?>dist/bundle.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>

