<div id="blog">

	<? foreach($pages as $page): ?>
	<div id="blog<?=$page['id']?>" class="blog">
		<hr/>
		<h2><?=$page['str_title']?></h2>
		<p><?=$page['niceDate']?></p>
		<p><?=$page['txt_text']?></p>
		<? if (isset($page['comments'])): ?>
			<?=$page['comments']?>
		<? endif ?>
	</div>
	<? endforeach; ?>

</div>
