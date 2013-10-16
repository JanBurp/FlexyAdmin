<div id="comments">
	<hr/>
	<div id="comment_form"><?=$form?></div>

	<? foreach($items as $item): ?>
	<div id="comment_<?=$item['id']?>" class="comment">
		<hr/>
		<h2><?=$item['str_title']?></h2>
		<p><?=$item['str_name']?> | <?=$item['niceDate']?></p>
		<p><?=$item['txt_text']?></p>
	</div>
	<? endforeach; ?>

</div>
