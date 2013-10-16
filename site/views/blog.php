<div id="blog">

  <?php if (!empty($items)): ?>

  	<? foreach($items as $item): ?>
    	<div id="blog<?=$item['id']?>" class="blog">
    		<hr/>
    		<h2><?=$item['str_title']?></h2>
    		<p><?=$item['niceDate']?></p>
    		<? if (isset($item['comments_count'])): ?><?=$item['comments_count']?><? endif ?>
    		<p><?=$item['txt_text']?></p>
        <?php if (isset($item['read_more_url'])): ?>
          <a href="<?=$item['read_more_url']?>"><?=$read_more?></a>
        <?php endif ?>
    	</div>
  	<? endforeach; ?>
  
    <?php if (!empty($pagination)): ?>
    	<hr/>
      <div class="pagination"><?=$pagination?></div> 
    <?php endif ?>

  <?php endif ?>

</div>
