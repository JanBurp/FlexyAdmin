<div id="comments">
	<hr/>
	<h1><?=lang('comments_title')?></h1>

	<div id="commentErrors">
		<?=$errors?>
	</div>

	<div id="commentForm">
		<?=$form?>
	</div>

	<? foreach($pages as $page): ?>
	<div id="comment<?=$page['id']?>" class="comment">
		<hr/>
		<h2><?=$page['str_title']?></h2>
		<p><?=$page['str_name']?> | <?=$page['niceDate']?></p>
		<p><?=$page['txt_text']?></p>
	</div>
	<? endforeach; ?>

</div>
