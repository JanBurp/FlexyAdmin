<table <?=$attributes?>>

<thead>
	<? if (!empty($title)): ?>
	<tr class="flexyCaption">
		<td class="flexyTitle" colspan="<?=$nrColumns?>"><?=$title?></td>
	</tr>
	<? endif; ?>

	<? if (!empty($headings)): ?>
	<tr class="flexyHeadings">
	<? foreach ($headings as $key=>$heading) : ?>
		<th class="<?=$key?>"><?=$heading?></th>
	<? endforeach; ?>
	</tr>
	<? endif; ?>	
</thead>

<tbody>
<? foreach ($data as $rowKey => $row): ?>
	<tr id="<?=$rowKey?>">
	<? foreach ($row as $colKey => $col) : ?>
		<td class="<?=$colKey?>"><?=$col;?></td>
	<? endforeach; ?>
	</tr>
<? endforeach; ?>
</tbody>

</table>
