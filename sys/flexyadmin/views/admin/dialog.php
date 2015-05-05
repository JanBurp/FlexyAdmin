<html>
<head>
	<title>FlexyAdmin</title>
	<link rel="stylesheet" href="<?=admin_assets()?>/css/admin_main.css" type="text/css" />
</head>

<body>

<div id="dialog">

	<p id="question"><?=$question?></p>
	<p id="text"><?=$text?></p>

	<?php foreach ($buttons as $name=>$uri): ?>
	<a class="button" id="button_<?=$name?>" href="<?=site_url($uri)?>"><?=$name;?></a>
	<?php endforeach; ?>

</div>

</body>
</html>