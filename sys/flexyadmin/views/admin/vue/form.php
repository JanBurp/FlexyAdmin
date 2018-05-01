<flexy-form
  <?php if (isset($action)): ?>
  action="<?=$action?>"
  <?php endif ?>
  formtype="single"
  name="<?=$name?>"
  title="<?=$title?>"
  <?php if (isset($fields)): ?>
  :fields="<?=htmlentities(array2json($fields),ENT_QUOTES, 'UTF-8')?>"
  <?php endif ?>
  <?php if (isset($id)): ?>
  :primary="<?=$id?>"
  <?php endif ?>
  <?php if (isset($disabled)): ?>
  :disabled="<?=$disabled?'true':'false';?>"
  <?php endif ?>
></flexy-form>
