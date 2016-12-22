<flexy-form
  title="<?=$title?>"
  name="<?=$name?>"
  path="<?=$path?>"
  :primary="<?=$id?>"
  :fields='<?=array2json($fields)?>'
  :fieldsets='<?=htmlentities(array2json($fieldsets),ENT_QUOTES, 'UTF-8')?>'
  :data='<?=htmlentities(array2json($data),ENT_QUOTES, 'UTF-8')?>'
  :options='<?=htmlentities(array2json($options),ENT_QUOTES, 'UTF-8')?>'
></flexy-form>
