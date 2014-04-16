<div class="<?=(isset($inherited))?'inherited':''?>">
  <h2 class="doc_function_method"><?=$name?>(
    <?php if (!empty($params)): $first=true;?>
  <?php foreach ($params as $key => $param): ?>
    <?php if (!$first): ?><span class="doc_seperator"><span class="doc_param_type">, </span></span><?endif;?>
    <?php if (isset($param['default'])): ?>[<?php endif ?>
    <span class="doc_param_type">(<?=$param['type']?>) </span><span class="doc_param_name">$<?=str_replace(array('[',']'),array('=',''),$param['param'])?></span>
    <?php if (isset($param['default'])): ?>]<?php endif ?>
  <? $first=false; endforeach ?>  
  <?php endif ?>
  )</h2>

  <div>
    <?php if (isset($inherited)): ?>
    <p class="doc_info doc_inherited">inherited from: <a href="<?=$inherited?>.html"><?=$inherited?></a></p>
    <?php endif ?>
    
    <?php if (!empty($shortdescription)): ?><p class="doc_description"><?=$shortdescription?></p><?php endif ?>
    
    <div class="doc_param_box">
      <?php if (!empty($params)): ?>
      <h4 class="doc_label">parameters:</h4>
      <ul class="doc_params">
      <?php foreach ($params as $key => $param): ?>
        <li class="doc_param"><p class="doc_param">
          (<?=$param['type']?>) $<?=str_replace('[',' [= ',$param['param'])?> <?=$param['desc']?>
        </p></li>
      <?php endforeach ?>  
      </ul>
      <?php endif ?>

      <h4 class="doc_label">return: </h4> 
      <ul class="doc_params">
        <li>
      <?php if (!empty($return)): ?>
        (<?=$return['type']?>) <?=$return['param']?> <?=$return['desc']?>
      <? else: ?>
        (void)
      <?php endif ?>
        </li>
      </ul>
    </div>

    <?php if (!empty($description)): ?><p class="doc_description"><?=$description?></p><?php endif ?>
    <br />
  </div>
</div>