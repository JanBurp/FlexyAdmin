<div id="search_results">
<h2>zoekresultaat van: '<?=$search?>'</h2>
<? if ($items): ?>
	<ul>
	<? foreach($items as $item): ?>
		<li class="result"><a href="<?=$item["uri"]?>"><?=$item["str_title"]?></a><br/><i><?=$item['txt_text']?></i></li>
	<? endforeach; ?>
	</ul>
<? else: ?>
<p>Geen resultaten gevonden voor '<?=$search?>'</p>
<? endif; ?>
</div>
