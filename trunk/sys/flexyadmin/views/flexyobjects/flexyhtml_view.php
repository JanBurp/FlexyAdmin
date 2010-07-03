<div <?=$attributes?>>

	<? if (!empty($title)): ?>
	<h1><?=$title?></h1>
	<? endif; ?>
	
	<? foreach ($data as $key => $value):?>
	<h2><?=$key?></h2><p><?=$value?></p>
	<? endforeach; ?>

</div>