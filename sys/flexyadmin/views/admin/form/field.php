<div class="<?=$container_class?>">
  <label for="<?=$name?>" class="<?=$label_class?> <?=($horizontal_bootstrap)?'col-sm-2':'';?>"><?=$label?></label>
  <?php if (isset($validation_error)): ?><?=$validation_error?><?php endif ?>
  <?php if ($horizontal_bootstrap): ?><div class="col-sm-10"><?php endif ?>
  <?=$control?>
  <?php if ($horizontal_bootstrap): ?></div><?php endif ?>
</div>