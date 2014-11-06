<?php foreach ($properties as $name => $property): ?>
<div class="panel panel-info <?=(isset($property['inherited']))?'inherited':''?>">
  <div class="panel-heading" data-toggle="collapse" data-target="#content_property_<?=$title?>_<?=$name?>">
    <h3 class="panel-title"><?=$property['nice_property']?></h3>
  </div>
  <div id="content_property_<?=$title?>_<?=$name?>" class="panel-body collapse">
    <?php if (isset($property['inherited'])): ?>
    <p class="doc_info doc_inherited">inherited from: <a href="<?=$property['inherited']?>.html"><?=$property['inherited']?></a></p>
    <?php endif ?>
    <?php if (!empty($property['shortdescription'])): ?><p class="doc_description"><?=$property['shortdescription']?></p><?php endif ?>
    <?php if (!empty($property['description'])): ?><p class="doc_description"><?=$property['description']?></p><?php endif ?>
  </div>
</div>
<?php endforeach ?>
