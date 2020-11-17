<!DOCTYPE html>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin - <?=strip_tags($str_title)?></title>
	<base href="<?=base_url()?>" />

  <link rel="shortcut icon" href="about:blank">
  <link rel="stylesheet" href="<?=mix_asset('flexyadmin.css',true)?>" type="text/css" media="screen">
</head>

<body>

<div id="main" :class="{'help':global.helpIsOn()}">
  <div class="container-fluid">
    <progress class="progress" v-show="state.progress > 0" :value="state.progress" max="100"></progress>
    <flexy-messages v-show="state.messages.length > 0" :messages="state.messages"></flexy-messages>
    <flexy-modal :options="state.modal"></flexy-modal>

    <div id="header" class="navbar navbar-fixed-top row align-items-start">

      <div class="col navbar-brand">
        <a href="<?=site_url($base_url)?>" title="FlexyAdmin <?=$build?>">
          <span class="flexy-block home-button btn btn-secondary">
            <span class="fa fa-home fa-lg"></span>
          </span>
        </a>
        <span class="flexy-block menu-button btn btn-secondary d-md-none" :class="{'bg-primary':global.menuIsVisible()}" @click="global.toggleMenu()">
          <span class="fa fa-bars fa-lg"></span>
        </span>

        <flexy-blocks v-once href="<?=site_url($base_url)?>" text="<?=$admin_title?>" class="title"></flexy-blocks>
      </div>

      <div class="col-auto navbar-nav">
        <?=$headermenu?>
      </div>
    </div>

    <div id="row" class="row">
      <div id="flexy-menu-side" class="col-md-2 d-md-block" :class="{'d-none':!global.menuIsVisible()}" @click="global.toggleMenu()">
        <?=$sidemenu?>
      </div>
      <div id="content" class="col-md-10">
        <router-view v-if="($route.path.length>1 && $route.path.substr(0,5)!=='/load')"></router-view>
        <template v-else><?=$content?></template>
      </div>
    </div>

    <div id="mask" v-cloak v-show="state.progress>0">
      <span class="spinner fa fa-spinner fa-pulse fa-fw"></span>
    </div>
  </div>

  <div id="help" v-cloak v-show="global.helpIsOn()">
    <flexy-accordion :items="state.help_items"></flexy-accordion>
  </div>

</div>




<script type="text/javascript" charset="utf-8">
var _flexy = {
  'index_page'    : '<?=$index_page?>',
  'base_url'      : '<?=preg_replace("/(.*\/_admin).*/u", "$1", $_SERVER["REQUEST_URI"]);?>',
  'auth_token'    : '<?=$user['auth_token']?>',
  'media_view'    : '<?=$user['str_filemanager_view']?>',
  'max_uploadsize': <?=$this->config->item('MAX_UPLOADSIZE')?>,
  'language'      : '<?=$language?>',
  'language_keys' : JSON.parse('<?=addslashes(array2json($lang_keys))?>'),
  'tinymceOptions': '<?=array2json($tinymceOptions)?>',
};
</script>
<script src="<?=mix_asset('main.build.js',true)?>" type="text/javascript" charset="utf-8"></script>

</body>
</html>

