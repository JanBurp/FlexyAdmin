<div id="forms">
  <h1><?=$title?></h1>
  <? if (!empty($errors)): ?>
  <div id="validation_errors"><?=$errors?></div>  
  <? endif ?>
  <?=$form;?>
</div>
