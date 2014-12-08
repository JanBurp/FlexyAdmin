<div class="<?=$field['container_class']?>">
  <?php if (isset($field['validation_error'])): ?><?=$field['validation_error']?><?php endif ?>
  <label for="<?=$field['name']?>" class="<?=$field['label_class']?> <?=($field['horizontal_bootstrap'])?'col-sm-2':'';?>"><?=$field['label']?></label>
  <?php if ($field['horizontal_bootstrap']): ?><div class="col-sm-10"><?php endif ?>
  <?=$field['control']?>
  <?php if ($field['horizontal_bootstrap']): ?></div><?php endif ?>
  <?php if (isset($field['html'])): ?>
  <div class="<?=$styles['field_html']?>"><?=$field['html']?></div>
  <?php endif ?>
</div>