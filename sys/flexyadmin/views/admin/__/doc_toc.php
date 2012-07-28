<h1>Inhoud</h1>
<div class="doc_column">
<? foreach ($toc as $key => $files): ?>

<? if ($files=='|'): ?>
  </div>
  <div class="doc_column">
<? else: ?>
  <h4><?=ucfirst($key)?></h4>
  <ul>
  <? foreach ($files as $name=>$link): ?>
    <li><a href="../../<?=$link?>"><?=$name?></a></li>
  <? endforeach ?>
  </ul>
<? endif; ?>

<? endforeach ?>
</div>
