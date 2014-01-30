<div id="forms">
  <?php if (isset($title)): ?><h1><?=$title?></h1><?php endif ?>
  <?php if (isset($errors)): ?><?=$errors?><?php endif ?>
  <?=$form;?>
</div>
