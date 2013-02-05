<? if (!empty($errors)): ?>
<div id="validation_errors"><?=$errors?></div>  
<? endif ?>
<? if (!empty($form)): ?>
<?=$form;?>  
<? endif ?>

<? if (!empty($message)): ?>
<strong><?=$message?></strong>  
<? endif ?>

