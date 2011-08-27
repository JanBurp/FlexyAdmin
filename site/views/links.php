<div id="links">
<h1>links</h1>
<p>
<? foreach($links as $link): ?>
	<a href="<?=$link["url_url"]?>" target="_blank"><?=$link["str_title"]?></a><br />
<? endforeach; ?>
</p>
</div>
