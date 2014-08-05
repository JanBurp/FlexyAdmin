<div id="module_links">
  <h1>Links</h1>
  <? foreach($links as $link): ?>
  	<a href="<?=$link["url_url"]?>" target="_blank"><?=$link["str_title"]?></a><br />
  <? endforeach; ?>
</div>