<h1>Safe Assets</h1>
<h2>Checked and .htaccess added:</h2>
<ul>
<?php foreach ($checked as $key => $value): ?>
  <li><?=$key?>: (<?=$value?>)</li>
<?php endforeach ?>
</ul>

<?php if (!empty($removed)): ?>
<h2 class="error">POSSIBLE HARMFULL FILES REMOVED:</h2>
<ul>
<?php foreach ($removed as $path => $files): ?>
  <li><?=$path?>:
    <ul>
      <?php foreach ($files as $file): ?>
        <li><?=$file?></li>
      <?php endforeach ?>
    </ul>
  </li>
<?php endforeach ?>
</ul>
<?php endif ?>
