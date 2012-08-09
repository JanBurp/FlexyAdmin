<h1>Safe Assets</h1>
<h2>Checked and .htaccess added:</h2>
<ul>
<? foreach ($checked as $key => $value): ?>
  <li><?=$key?>: (<?=$value?>)</li>
<? endforeach ?>
</ul>

<? if (!empty($removed)): ?>
<h2 class="error">POSSIBLE HARMFULL FILES REMOVED:</h2>
<ul>
<? foreach ($removed as $path => $files): ?>
  <li><?=$path?>:
    <ul>
      <? foreach ($files as $file): ?>
        <li><?=$file?></li>
      <? endforeach ?>
    </ul>
  </li>
<? endforeach ?>
</ul>
<? endif ?>
