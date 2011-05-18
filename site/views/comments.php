<div id="comments">
	<hr/>
	<h1>Comments</h1>

	<div id="commentErrors">
		<?=$errors?>
	</div>

	<div id="commentForm">
		<?=$form?>
	</div>

	<? foreach($items as $item): ?>
	<div id="comment<?=$item['id']?>" class="comment">
		<hr/>
		<h2><?=$item['str_titel']?></h2>
		<p><?=$item['str_naam']?> | <?=$item['niceDate']?></p>
		<p><?=$item['txt_opmerkingen']?></p>
	</div>
	<? endforeach; ?>

</div>
