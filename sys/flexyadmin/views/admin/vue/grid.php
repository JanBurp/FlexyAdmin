<flexy-grid
  title="<?=$title?>"
  name="<?=$name?>"
  :fields='<?=array2json($fields)?>'
  :data='<?=array2json($data)?>'
  order="<?=$order?>"
  find="<?=$find?>"
  :info='<?=json_encode($info)?>'
  <?php if (isset($type)): ?> type='<?=$type?>'<?php endif ?>
></flexy-grid>
