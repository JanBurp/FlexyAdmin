<div class="<?=$field['container_class']?> field_<?=$field['name']?> fieldtype_<?=get_prefix( $field['name'],'_')?> <?=$field['status']?> row" <?=$attributes?>>
  <?php if (isset($field['validation_error'])): ?><?=$field['validation_error']?><?php endif ?>
  <label for="<?=$field['name']?>" class="<?=$field['label_class']?> <?=($field['horizontal_bootstrap'])?'col-lg-2 col-sm-3':'';?>"><?=$field['label']?></label>
  <?php if ($field['horizontal_bootstrap']): ?><div class="col-lg-10 col-sm-9"><?php endif ?>
    <?=$field['control']?>
  <?php if ($field['horizontal_bootstrap']): ?></div><?php endif ?>
  <?php if (isset($field['html']) and !empty($field['html'])): ?>
  <div class="<?=$styles['field_html']?>"><?=$field['html']?></div>
  <?php endif ?>
</div>