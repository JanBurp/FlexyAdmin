<div <?=$attributes_render?>>
	<h1><?=$title?></h1>
	
	<? foreach ($data as $key => $value):?>
		<h2><?=$key?></h2><p><?=$value?></p>
	<? endforeach; ?>

</div>