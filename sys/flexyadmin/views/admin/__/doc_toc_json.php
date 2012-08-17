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

var links = {
  'Index' : root+'index.html',
<? foreach ($toc as $key => $files): ?>
<? if ($files!='|'): ?>
<? foreach ($files as $name=>$link): ?>
'<?=$name?>' : root+'<?=str_replace('userguide/FlexyAdmin/','',$link)?>',
<? endforeach ?>
<? endif ?>
<? endforeach ?>
};

var index = '<?= preg_replace("/>(\s*)</uUs", "><", $html) ?>';