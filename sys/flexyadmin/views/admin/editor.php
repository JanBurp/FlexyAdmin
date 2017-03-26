<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin</title>
	<base href="<?=base_url()?>" />

  <link rel="stylesheet" href="<?=admin_assets()?>css/font-awesome.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="<?=admin_assets()?>dist/flexyadmin.css" type="text/css" media="screen">
</head>

<body>

<div id="main">

  <flexy-grid
    type   = "media"
    api    = "table"
    name   = "pictures"
    title  = "Afbeeldingen"
    offset = "0"
    limit  = "10"
    order  = ""
    filter = ""
  ></flexy-grid>

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

