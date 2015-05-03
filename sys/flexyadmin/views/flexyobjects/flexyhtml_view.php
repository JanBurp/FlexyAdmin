<div <?=$attributes?>>

	<?php if (!empty($title)): ?>
	<h1><?=$title?></h1>
	<?php endif; ?>
	
	<?php foreach ($data as $key => $value):?>
	<h2><?=$key?></h2><p><?=$value?></p>
	<?php endforeach; ?>

</div>