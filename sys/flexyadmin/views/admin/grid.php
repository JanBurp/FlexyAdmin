<table class="<?=$class?>" order="<?=$order?>" search="<?=$search?>" <?php if (isset($pagination['offset'])):?>offset="<?=$pagination['offset']?>" url="<?=$pagination['base_url']?>"<?php endif;?>>

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

		<?php if (isset($heading["row"])): ?>
			<tr class="heading <?=$heading["class"];?>">
			<?php foreach($heading["row"] as $cell): ?>
				<th class="<?=$cell["class"];?>"><?=$cell["cell"];?></th>
			<?php endforeach; ?>
			</tr>
		<?php endif; ?>

	</thead>


	<tbody>
	<?php if (isset($rows) and !empty($rows)):
	 		foreach($rows as $row): ?>
			<tr id="<?=$row["id"]?>" class="<?=$row["class"];?>">
				<?php foreach($row["row"] as $cell): ?>
				<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
				<?php endforeach; ?>
			</tr>
	<?php endforeach; ?>
  <?php endif; ?>
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
