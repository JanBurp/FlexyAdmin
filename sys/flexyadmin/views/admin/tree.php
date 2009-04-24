<table class="grid <?=$class?>">

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
		<tr class="tree">
			<td colspan="100">
				<table>
					<tr>
						<td class="tree"><?=$tree?></td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
	</table>