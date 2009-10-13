<table class="thumbs <?=$class?>">

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
		
	</thead>

	<tbody>
	<tr><td colspan="100">

	<? if (isset($rows) and !empty($rows)):
			foreach($rows as $row): ?>
				<div class="file <?=$row["class"]?>">
					<div class="thumb"><?=$row["row"][0]["cell"];?></div>
					<div class="name"><?=$row["id"];?></div>
				</div>
	<? endforeach;
		endif; ?>
	</td></tr>
	</tbody>

</table>