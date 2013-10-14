<div id="blog_latest">
  <h1>Laatste nieuws</h1>

	<? foreach($items as $item): ?>
	<div class="blog">
		<a href="<?=$item['read_more_url']?>"><h2><?=$item['str_title']?></h2></a>
	</div>
	<? endforeach; ?>
</div>
