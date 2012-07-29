var toc = {
<? foreach ($toc as $key => $files): ?>
<? if ($files!='|'): ?>
<? foreach ($files as $name=>$link): ?>
'<?=$link?>' : { name : '<?=$name?>', type : '<?=ucfirst($key)?>', keywords : '' },
<? endforeach ?>
<? else: ?>
'|' : '|',
<? endif; ?>
<? endforeach ?>
};

var index = '<?=$html?>';