<flexy-form
  title="<?=$title?>"
  name="<?=$name?>"
  :primary="<?=$id?>"
  :fields='<?=array2json($fields)?>'
  :fieldsets='<?=array2json($fieldsets)?>'
  :data='<?=htmlentities(array2json($data),ENT_QUOTES, 'UTF-8')?>'>
</flexy-form>
