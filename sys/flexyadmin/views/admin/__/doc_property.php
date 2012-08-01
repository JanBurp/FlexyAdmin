<div class="<?=(isset($inherited))?'inherited':''?>">
  <h2 class="doc_property"><? if (!empty($type[0])): ?>(<?=$type[0]?>)<? endif ?> <?=$name?></h2>
  <div>
    <? if (isset($inherited)): ?>
    <p class="doc_info doc_inherited">inherited from: <a href="<?=$inherited?>.html"><?=$inherited?></a></p>
    <? endif ?>
    <? if (!empty($shortdescription)): ?><p class="doc_description"><?=$shortdescription?></p><? endif ?>
    <? if (!empty($description)): ?><p class="doc_description"><?=$description?></p><? endif ?>
    <br />
  </div>
</div>