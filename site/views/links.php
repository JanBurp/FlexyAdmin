<div id="links">
<h2>links</h2>
<? foreach($links as $link): ?>
	<p><a href="<?=$link["url_url"]?>" target="_blank"><?=$link["str_title"]?></a></p>
<? endforeach; ?>
</div>
