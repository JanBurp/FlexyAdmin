<?php foreach ($methods as $name => $method): ?>
<div class="panel panel-info <?=(isset($method['inherited']))?'inherited':''?>">
  <div class="panel-heading" data-toggle="collapse" data-target="#content_<?=$title?>_<?=$name?>">
    <h3 class="panel-title"><?=$method['nice_name']?></h3>
  </div>
  <div id="content_<?=$title?>_<?=$name?>" class="panel-body collapse">
    <?php if (isset($method['inherited'])): ?><p class="doc_info doc_inherited">inherited from: <a href="<?=$method['inherited']?>.html"><?=$method['inherited']?></a></p><?php endif ?>
    
    <?php if (!empty($method['doc']['shortdescription'])): ?><p class="doc_description"><?=$method['doc']['shortdescription']?></p><?php endif ?>
      
    <div>
      <?php if (!empty($method['doc']['param'])): ?>
        <h4>parameters:</h4>
        <ul>
        <?php foreach ($method['doc']['param'] as $key => $param): ?>
          <li class="clean_code"><?=$param['nice_param']?></li>
        <?php endforeach ?>  
        </ul>
      <?php endif ?>

      <h4>return: </h4> 
      <ul>
        <li class="clean_code"><?=$method['nice_return']?></li>
      </ul>
    </div>
      
    <?php if (!empty($method['doc']['description'])): ?><p class="doc_description"><?=$method['doc']['description']?></p><?php endif ?>
  </div>
</div>
<?php endforeach ?>
