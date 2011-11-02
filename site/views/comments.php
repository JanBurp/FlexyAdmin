<div id="comments">
	<hr/>
	<h1><?=lang('title')?></h1>

	<div id="commentErrors">
		<?=$errors?>
	</div>

	<div id="commentForm">
		<?=$form?>
	</div>

	<? foreach($items as $item): ?>
	<div id="comment<?=$item['id']?>" class="comment">
		<hr/>
		<h2><?=$item['str_title']?></h2>
		<p><?=$item['str_name']?> | <?=$item['niceDate']?></p>
		<p><?=$item['txt_text']?></p>
	</div>
	<? endforeach; ?>

</div>
