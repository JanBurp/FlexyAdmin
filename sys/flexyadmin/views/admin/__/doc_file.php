<div>
  <h1 class="doc_file"><?=$file?></h1>
  <? if ($CIparent): ?>
    Dit is een uitbreiding op CodeIgniter's <a href="<?=$CIparent?>" target="_blank"><?=str_replace('MY_','',$file)?></a>.
  <? endif ?>
  <div class="doc_functions"><?=$functions?></div>
</div>