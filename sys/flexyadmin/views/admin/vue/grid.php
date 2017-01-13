<flexy-grid
  title="<?=$title?>"
  name="<?=$name?>"
  :fields='<?=array2json($fields)?>'
  api='<?=$api?>'
  <?php if (isset($data)): ?>:data='<?=array2json($data)?>'<?php endif ?>
  order="<?=$order?>"
  offset='<?=$offset?>'
  limit="<?=$limit?>"
  filter="<?=htmlentities($filter)?>"
  <?php if (isset($type)): ?> type='<?=$type?>'<?php endif ?>
></flexy-grid>
