<div id="blog">

	<? foreach($items as $item): ?>
	<div id="blog<?=$item['id']?>" class="blog">
		<hr/>
		<h2><?=$item['str_title']?></h2>
		<p><?=$item['niceDate']?></p>
		<p><?=$item['txt_text']?></p>
		<? if (isset($item['comments'])): ?>
			<?=$item['comments']?>
		<? endif ?>
	</div>
	<? endforeach; ?>

</div>
