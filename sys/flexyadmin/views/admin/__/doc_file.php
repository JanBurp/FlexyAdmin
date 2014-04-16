<div id="<?=str_replace(array('.php',' '),array('','_'),$file);?>" class="doc_page">
  <h1 class="doc_file"><?=$file?></h1>
  <?php if (isset($path)): ?><p class="doc_info doc_path"><?=$path?></p><?php endif ?>
  
  <?php if (isset($doc['author'])): ?>
  <p class="doc_author"><?=implode(' ',$doc['author'])?></p>
  <?php endif ?>
  
  <?php if (isset($shortdescription)): ?><p class="doc_description"><?=$shortdescription?></p><?php endif ?>
  <?php if (isset($description)): ?><p class="doc_description"><?=$description?></p><?php endif ?>
  
  <div class="doc_functions <?php if (isset($path)): ?>accordion<?php endif ?>"><?=$functions?></div>
</div>