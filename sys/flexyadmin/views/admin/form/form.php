<form id="<?=$id?>" form_id="<?=$form_id?>" class="<?=$class?>" action="<?=$action?>" method="<?=$method?>" role="form" accept-charset="utf-8" enctype="multipart/form-data">
  <input name="__form_id" id="__form_id" value="<?=$form_id?>" class="hidden" type="hidden">
  <?php foreach ($fieldsets as $fieldset): ?>
  <fieldset class="<?=$fieldset['class']?>" id="<?=$fieldset['id']?>">
    <?php if (!empty($fieldset['title'])): ?><legend><?=$fieldset['title']?></legend><?php endif ?>
    <?=$fieldset['fields']?>
  </fieldset>
  <?php endforeach ?>
</form>
