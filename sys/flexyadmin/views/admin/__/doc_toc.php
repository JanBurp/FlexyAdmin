<div class="doc_column">
<? foreach ($toc as $key => $files): ?>

<? if ($files=='|'): ?>
  </div>
  <div class="doc_column">
<? else: ?>
  <h1><?=ucfirst($key)?></h1>
  <? foreach ($files as $name=>$link): ?>
    <p><a href="../../<?=$link?>"><?=$name?></a></p>
  <? endforeach ?>
<? endif; ?>

<? endforeach ?>
</div>
