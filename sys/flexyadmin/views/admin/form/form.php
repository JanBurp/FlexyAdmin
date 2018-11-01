<form id="<?=$id?>" data-form_id="<?=$form_id?>" class="<?=$class?>" action="<?=$action?>" method="<?=$method?>" accept-charset="utf-8" enctype="multipart/form-data" novalidate="">
<input name="__form_id" id="__form_id" value="<?=$form_id?>" class="hidden" type="hidden">
<?php foreach ($fieldsets as $fieldset): ?>
  <?php if (!empty($fieldset['fields'])): ?>
    <fieldset class="<?=$fieldset['class']?>" id="<?=$fieldset['id']?>">
      <?php if (!empty($fieldset['title'])): ?><legend><?=$fieldset['title']?></legend><?php endif ?>
      <?=$fieldset['fields']?>
    </fieldset>
  <?php endif ?>
<?php endforeach ?>
</form>
