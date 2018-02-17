<!DOCTYPE html>
<html lang="<?=$language?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin</title>
	<base href="<?=base_url()?>" />

  <link rel="stylesheet" href="<?=admin_assets()?>dist/flexyadmin.css" type="text/css" media="screen">
  <style>
    html,body {
      width:100%;
      height:100%;
    }
    #main {
      overflow:auto;
    }
  </style>
</head>

<body>

<div id="main" class="editor-popup">

  <div class="card form">
    <div class="card-body">

      <!-- Alt -->
      <div class="form-group row" v-cloak>
        <label class="col-sm-3 form-control-label" for="alt" v-once>{{$lang.img_popup_alt}}</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="alt" name="alt" :value="mediaPopup.alt||'<?=$alt?>'">
        </div>
      </div>
      
      <!-- Mediapiacker -->
      <div class="form-group row" v-cloak>
        <label class="col-sm-3 form-control-label" for="alt" v-once>{{$lang.img_popup_src}}</label>
        <div class="col-sm-9">
          <mediapicker id="src" name="src" value="<?=$src?>" path="<?=$path?>" :autoresize="true" :openpicker="true" @input="mediaPopupChanged"></mediapicker>
        </div>
      </div>

    </div>
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

