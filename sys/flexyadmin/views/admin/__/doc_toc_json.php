var toc = {
<?php foreach ($toc as $key => $files): ?>
<?php if ($files!='|'): ?>
<?php foreach ($files as $name=>$link): ?>
"<?=$link?>" : { "name" : "<?=$name?>", "type" : "<?=ucfirst($key)?>", "keywords" : "" },
<?php endforeach ?>
<?php else: ?>
"|" : "|",
<?php endif; ?>
<?php endforeach ?>
};

var links = {
"Index" : root+'index.html',
<?php foreach ($toc as $key => $files): ?>
<?php if ($files!='|'): ?>
<?php foreach ($files as $name=>$link): ?>
"<?=$name?>" : root+'<?=str_replace('userguide/FlexyAdmin/','',$link)?>',
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
};

var test = {
"Index" : "index.html",
<?php foreach ($toc as $key => $files): ?>
<?php if ($files!='|'): ?>
<?php foreach ($files as $name=>$link): ?>
"<?=$name?>" : "<?=str_replace('userguide/FlexyAdmin/','',$link)?>",
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
};


var index = '<?= preg_replace("/>(\s*)</uUs", "><", $html) ?>';