<table class="<?=$class?>">

	<thead>
		<tr class="caption <?=$caption["class"];?>">
			<td colspan="100">
				<table>
					<thead>
						<tr>
							<?php foreach($caption["row"] as $cell): ?>
								<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
							<?php endforeach; ?>
						</tr>
					</thead>
				</table>
			</td>
		</tr>
	</thead>

	<tbody>
	<?php 
	if (isset($rows) and !empty($rows)) {
		?><tr><?php 
		foreach ($rows as $key => $value) {
			$row=$value['row'];
			?><td class='bar <?=$value['class']?>'>
				<div class="graphBarTop" style="height:<?=110-$row[1]['percentage']?>px">
				</div>
				<div class="graphBar" style="height:<?=$row[1]['percentage']?>px">
					<span class="verticalText" title="<?=$row[1]['value'];?>"><?=$row[1]['value'];?></span>
				</div>
			</td><?php 
		}
		?></tr>
		<tr><?php 
		foreach ($rows as $key => $value) {
			$row=$value['row'];
			?><td class='<?=$value['class']?>'><?=$row[0]['value'];?></td><?php 
		}
		?></tr><?php 
	}
	?>
	</tbody>

</table>