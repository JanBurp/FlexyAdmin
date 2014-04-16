<table <?=$attributes?>>

<thead>
	<?php if (!empty($title)): ?>
	<tr class="flexyCaption">
		<td class="flexyTitle" colspan="<?=$nrColumns?>"><?=$title?></td>
	</tr>
	<?php endif; ?>

	<?php if (!empty($headings)): ?>
	<tr class="flexyHeadings">
	<?php foreach ($headings as $key=>$heading) : ?>
		<th class="<?=$key?>"><?=$heading?></th>
	<?php endforeach; ?>
	</tr>
	<?php endif; ?>	
</thead>

<tbody>
<?php foreach ($data as $rowKey => $row): ?>
	<tr id="<?=$rowKey?>">
	<?php foreach ($row as $colKey => $col) : ?>
		<td class="<?=$colKey?>"><?=$col;?></td>
	<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</tbody>

</table>
