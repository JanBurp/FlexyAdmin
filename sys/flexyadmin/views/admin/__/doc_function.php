<div class="<?=(isset($inherited))?'inherited':''?>">
  <h2 class="doc_function_method"><?=$name?>(
  <? if (!empty($params)): ?>
  <? foreach ($params as $key => $param): ?>
    <? if (isset($param['default'])): ?>[<? endif ?>
    <span class="doc_param_type">(<?=$param['type']?>) </span><span class="doc_param_name">$<?=str_replace(array('[',']'),array('=',''),$param['param'])?></span>
    <? if (isset($param['default'])): ?>]<? endif ?>
    <span class="doc_param_type">,</span>
  <? endforeach ?>  
  <? endif ?>
  )</h2>
  
  <? if (isset($inherited)): ?>
  <p class="doc_info doc_inherited">inherited from: <a href="<?=$inherited?>.html"><?=$inherited?></a></p>
  <? endif ?>
  
  <p class="doc_description"><?=$shortdescription?></p>
  
  <? if (!empty($params)): ?>
  <h4 class="doc_label">parameters:</h4>
  <ul class="doc_params">
  <? foreach ($params as $key => $param): ?>
    <li class="doc_param"><p class="doc_param">
      (<?=$param['type']?>) $<?=str_replace('[',' [= ',$param['param'])?> <?=$param['desc']?>
    </p></li>
  <? endforeach ?>  
  </ul>
  <? endif ?>

  <h4 class="doc_label">return: </h4> 
  <ul class="doc_params">
    <li>
  <? if (!empty($return)): ?>
    (<?=$return['type']?>) <?=$return['param']?> <?=$return['desc']?>
  <? else: ?>
    (void)
  <? endif ?>
    </li>
  </ul>

  <p class="doc_description"><?=$description?></p>
  
  <? if (!empty($author)): ?><p class="doc_info doc_author">author: <?=$author[0]?></p><? endif ?>
  <p class="doc_info doc_lines">lines: <?=$lines?></p>
</div>