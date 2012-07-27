<div>
  <h2 class="doc_function_method">function <?=$name?>()</h2>
  
  <? if (!empty($params)): ?>
  <p class="doc_label">parameters:</p>
  <ul class="doc_params">
  <? foreach ($params as $key => $value): ?>
    <li class="doc_param"><p class="doc_param"><?=$value?></p></li>
  <? endforeach ?>  
  </ul>
  <? endif ?>

  <p class="doc_return">
  <span class="doc_label">return: </span> 
  <? if (!empty($return)): ?>
    <?=$return?>
  <? else: ?>
    (void)
  <? endif ?>
  </p>
  <p class="doc_description"><?=$description?></p>
  <? if (!empty($author)): ?><p class="doc_info doc_author">author: <?=$author?></p><? endif ?>
  <p class="doc_info doc_lines">lines: <?=$lines?></p>
</div>