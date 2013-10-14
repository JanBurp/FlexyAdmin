<div id="blog">

	<? foreach($items as $item): ?>
	<div id="blog<?=$item['id']?>" class="blog">
		<hr/>
		<h2><?=$item['str_title']?></h2>
		<p><?=$item['niceDate']?></p>
		<p><?=$item['txt_text']?></p>
    <?php if (isset($item['read_more_url'])): ?>
      <a href="<?=$item['read_more_url']?>"><?=$read_more?></a>
    <?php endif ?>
		<? if (isset($item['comments'])): ?>
			<?=$item['comments']?>
		<? endif ?>
	</div>
	<? endforeach; ?>
  
  <?php if (!empty($pagination)): ?>
	<hr/>
  <div class="pagination"><?=$pagination?></div> 
  <?php endif ?>

</div>
