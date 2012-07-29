<div class="<?=(isset($inherited))?'inherited':''?>">
  <h3 class="doc_property"><? if (!empty($type[0])): ?>(<?=$type[0]?>)<? endif ?> <?=$name?></h3>
  <? if (isset($inherited)): ?>
  <p class="doc_info doc_inherited">inherited from: <a href="<?=$inherited?>.html"><?=$inherited?></a></p>
  <? endif ?>
  <p class="doc_description"><?=$shortdescription?></p>
  <p class="doc_description"><?=$description?></p>
</div>