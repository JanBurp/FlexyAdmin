<?
$colNr=1;
$rowNr=1;

function pre($class) {
	$pre=get_prefix($class);
	if ($pre==$class) $pre='';
	return $pre;
}
function url($url,$page,$order) {
	return site_url($url)."/page/$page/order/$order";
}

?>


<table <?=$attributes?>>

<thead>
	<? if (!empty($title)): ?>
	<tr class="flexyCaption">
		<td class="flexyTitle" colspan="<?=$nrColumns?>"><?=$title?></td>
	</tr>
	<? endif; ?>

	<? if (!empty($pagination)): ?>
	<tr class="flexyPagination">
		<td colspan="<?=$nrColumns?>"><?=$pagination['render']?></td>
	</tr>
	<? endif; ?>


	<? if (!empty($headings)): $colNr=1; ?>
	<tr class="flexyHeadings">
	<? foreach ($headings as $key=>$heading) {
			$class='flexyOrder';
			$newOrder=$key;
			if ($order=='_'.$key) {
				$class.='Up';
			}
			elseif ($order==$key) {
				$class.='Down';
				$newOrder='_'.$key;
			}
	 ?>
		<th class="col<?=$colNr++?> <?=$key?> <?=pre($key)?> <?=$class?>"><a href="<?=url($url,$page,$newOrder)?>"><?=$heading?></a></th>
	<? } ?>
	</tr>
	<? endif; ?>
	
</thead>

<tbody class="flexyBody">
<? foreach ($data as $rowKey => $row): $colNr=1; ?>
	<tr id="<?=$rowKey?>" class="row<?=$rowNr++?> ">
	<? foreach ($row as $colKey => $col) : ?>
		<td class="col<?=$colNr++?> <?=$colKey?> <?=pre($colKey)?>" title="<?=$col;?>"><?=$col;?></td>
	<? endforeach; ?>
	</tr>
<? endforeach; ?>
</tbody>

<? if (!empty($pagination)): ?>
<tfoot>
<tr class="flexyPagination">
	<td colspan="<?=$nrColumns?>"><?=$pagination['render']?></td>
</tr>
</tfoot>
<? endif; ?>


</table>
