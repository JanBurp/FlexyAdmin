<h1>Inhoud</h1>
<div class="doc_column">
<?php foreach ($toc as $key => $files): ?>

<?php if ($files=='|'): ?>
  </div>
  <div class="doc_column">
<?php else: ?>
  <h4><?=ucfirst($key)?></h4>
  <ul>
  <?php foreach ($files as $name=>$link): ?>
    <li><a href="../../<?=$link?>"><?=$name?></a></li>
  <?php endforeach ?>
  </ul>
<?php endif; ?>

<?php endforeach ?>
</div>
