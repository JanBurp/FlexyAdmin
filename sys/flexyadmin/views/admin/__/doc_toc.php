<h1>Inhoud</h1>
<div id="toc">
<?php foreach ($toc as $key => $files): ?>
  <?php if ($files!='|' and count($files)>0): ?>
    <div class="doc_column doc__<?=safe_string($key)?>">
      <h4><?=ucfirst($key)?></h4>
      <ul>
      <?php foreach ($files as $name=>$link): ?>
        <li><a href="../../<?=$link?>"><?=$name?></a></li>
      <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>
<?php endforeach ?>
</div>
