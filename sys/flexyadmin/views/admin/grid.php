<table
  class="<?=$class?>"
  order="<?=$order?>"
  search="<?=$search?>"
  <?if (isset($pagination['offset'])):?>offset="<?=$pagination['offset']?>" url="<?=$pagination['base_url']?>"<?endif;?>
  <?if (isset($edit_types)):?>data-edit_types="<?=$edit_types?>"<?endif;?>
  >

	<thead>
		<tr class="caption <?=$caption["class"];?>">
			<td colspan="100">
				<table>
					<thead>
						<tr>
							<? foreach($caption["row"] as $cell): ?>
								<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
							<? endforeach; ?>
						</tr>
					</thead>
				</table>
			</td>
		</tr>

		<? if (isset($heading["row"])): ?>
			<tr class="heading <?=$heading["class"];?>">
			<? foreach($heading["row"] as $cell): ?>
				<th class="<?=$cell["class"];?>"><?=$cell["cell"];?></th>
			<? endforeach; ?>
			</tr>
		<? endif; ?>

	</thead>


	<tbody>
	<? if (isset($rows) and !empty($rows)):
	 		foreach($rows as $row): ?>
			<tr id="<?=$row["id"]?>" class="<?=$row["class"];?>">
				<? foreach($row["row"] as $cell): ?>
				<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
				<? endforeach; ?>
			</tr>
	<? endforeach;
		endif; ?>
	</tbody>
	
	<? if (isset($pagination['links'])): ?>
	<tfoot>
		<tr class="pagination">
			<td colspan="100">
				<?=$pagination['links']?>
			</td>
		</tr>
	</tfoot>
	<? endif; ?>
	
	</table>