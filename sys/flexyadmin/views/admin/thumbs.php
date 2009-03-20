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
	</thead>

	<tbody>
	<tr><td colspan="100">

	<? if (isset($rows) and !empty($rows)):
			foreach($rows as $row): ?>
				<div class="file <?=$row["class"]?>">
					<div  class="toolbar"><?=$row["row"][0]["cell"];?></div>
					<div class="thumb"><?=$row["row"][1]["cell"];?></div>
					<div  class="name"><?=$row["id"];?></div>
				</div>
	<? endforeach;
		endif; ?>
	</td></tr>
	</tbody>

</table>