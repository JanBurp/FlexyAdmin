<html>
<head>
	<title>FlexyAdmin V1</title>
	<link rel="stylesheet" href="<?=admin_assets()?>/css/admin_main.css" type="text/css" />
</head>

<body>

<div id="dialog">

	<p id="question"><?=$question?></p>
	<p id="text"><?=$text?></p>

	<? foreach ($buttons as $name=>$uri): ?>
	<a class="button" id="button_<?=$name?>" href="<?=site_url($uri)?>"><?=$name;?></a>
	<? endforeach; ?>

</div>

</body>
</html>