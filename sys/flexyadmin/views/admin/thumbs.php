<table class="thumbs <?=$class?>" <?php if(isset($pagination['offset'])):?>offset="<?=$pagination['offset']?>" url="<?=$pagination['base_url']?>"<?php endif;?> order="<?=$order?>" data-search="<?=$search?>">

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
	<tr><td colspan="100">

	<?php if (isset($rows) and !empty($rows)):
			foreach($rows as $row): ?>
				<div class="file <?=$row["class"]?>">
					<div  class="toolbar"><?=$row["row"][0]["cell"];?></div>
					<div class="thumb"><?=$row["row"][1]["cell"];?></div>
					<div  class="name"><?=$row["id"];?></div>
				</div>
	<?php endforeach;
		endif; ?>
	</td></tr>
	</tbody>
	
	<?php if (isset($pagination['links'])): ?>
	<tfoot>
		<tr class="pagination">
			<td colspan="100">
				<?=$pagination['links']?>
			</td>
		</tr>
	</tfoot>
	<?php endif; ?>
	
</table>