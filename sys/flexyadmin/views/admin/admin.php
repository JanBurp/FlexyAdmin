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

<body class="<?=$class?>">

<div id="main" :class="{'help':global.helpIsOn()}">
  <div class="container-fluid">
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
      <div id="flexy-menu-side" class="col-sm-2">

      <router-link to="/foo">Go to Foo</router-link>
      <router-link to="/bar">Go to Bar</router-link>
      <router-link to="/grid/tbl_menu">Menu</router-link>
      <router-link to="/grid/tbl_links">Links</router-link>

      <?=$sidemenu?>
      </div>
      <div id="content" class="col-sm-10">

        <router-view></router-view>
        
        <template v-if="!state.menu"><?=$content?></template>
        
        <template v-if="state.menu">
          <template v-if="state.menu.type=='grid'">
            GRID: {{state.menu}}

            <flexy-grid type="table" :api="state.menu.api" :name="state.menu.table" :title="state.menu.title" offset="0" :autoresize="true"></flexy-grid>

          </template>
          <template v-else-if="state.menu.type=='media'">MEDIA: {{state.menu}} </template>
          <template v-else>{{state.menu}}</template>
        </template>
      
      </div>
    </div>
    <div id="mask" v-cloak v-show="state.progress>0">
      <span class="spinner fa fa-spinner fa-pulse fa-fw"></span>
    </div>
  </div>
  
  <div id="help" v-cloak v-show="global.helpIsOn()">
    <flexy-accordion :items="state.help_items">
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
<script src="<?=admin_assets()?>dist/bundle.js" type="text/javascript" charset="utf-8"></script>

</body>
</html>

