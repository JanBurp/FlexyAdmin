<div id="<?=str_replace(array('.php',' '),array('','_'),$file);?>" class="doc_page">
  <h1 class="doc_file"><?=$file?></h1>
  <? if (isset($path)): ?><p class="doc_info doc_path"><?=$path?></p><? endif ?>
  <div class="doc_functions"><?=$functions?></div>
</div>