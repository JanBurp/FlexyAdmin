<div id="search_results">
<h2>zoekresultaat van: '<?=$search?>'</h2>

<? if ($items): ?>

	<? if (isset($config['group_result_by_uris']) and $config['group_result_by_uris']): ?>
		
		<? foreach ($items as $group): ?>
			<? if (!empty($group['result'])): ?>
				<br />
				<h3>in: '<?=$group['title']?>'</h3>
				<ul>
				<? foreach($group['result'] as $item): ?>
					<li class="result"><a href="<?=$item["uri"]?>"><?=$item["str_title"]?></a><br/><i><?=$item['txt_text']?></i></li>
				<? endforeach; ?>
				</ul>
			<? endif ?>
		<? endforeach ?>
	
	<? else: ?>
		<ul>
		<? foreach($items as $item): ?>
			<li class="result"><a href="<?=$item["uri"]?>"><?=$item["str_title"]?></a><br/><i><?=$item['txt_text']?></i></li>
		<? endforeach; ?>
		</ul>
	<? endif ?>

<? else: ?>
<p>Geen resultaten gevonden voor '<?=$search?>'</p>
<? endif; ?>
</div>
