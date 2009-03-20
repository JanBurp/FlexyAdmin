<table class="<?=$class?>">

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

		<tr class="heading <?=$heading["class"];?>">
		<? foreach($heading["row"] as $cell): ?>
			<th class="<?=$cell["class"];?>"><?=$cell["cell"];?></th>
		<? endforeach; ?>
		</tr>
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
	</table>