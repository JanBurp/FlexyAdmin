<div class="<?=(isset($inherited))?'inherited':''?>">
  <h2 class="doc_property"><?php if (!empty($type[0])): ?>(<?=$type[0]?>)<?php endif ?> <?=$name?></h2>
  <div>
    <?php if (isset($inherited)): ?>
    <p class="doc_info doc_inherited">inherited from: <a href="<?=$inherited?>.html"><?=$inherited?></a></p>
    <?php endif ?>
    <?php if (!empty($shortdescription)): ?><p class="doc_description"><?=$shortdescription?></p><?php endif ?>
    <?php if (!empty($description)): ?><p class="doc_description"><?=$description?></p><?php endif ?>
    <br />
  </div>
</div>