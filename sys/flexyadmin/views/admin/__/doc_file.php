<div id="<?=str_replace(array('.php',' '),array('','_'),$file);?>" class="doc_page">
  <h1 class="doc_file"><?=$file?></h1>
  <? if (isset($path)): ?><p class="doc_info doc_path"><?=$path?></p><? endif ?>
  
  <? if (isset($doc['author'])): ?>
  <p class="doc_author"><?=implode(' ',$doc['author'])?></p>
  <? endif ?>
  
  <? if (isset($shortdescription)): ?><p class="doc_description"><?=$shortdescription?></p><? endif ?>
  <? if (isset($description)): ?><p class="doc_description"><?=$description?></p><? endif ?>
  
  <div class="doc_functions <? if (isset($path)): ?>accordion<? endif ?>"><?=$functions?></div>
</div>