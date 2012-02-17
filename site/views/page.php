<? if (!$break): ?>
  <h1><?=ascii_to_entities($str_title);?></h1>
  <div class="text"><?=$txt_text?></div>

  <? if (!empty($media_foto)) : ?>
  <div class="photo"><img src="<?=SITEPATH?>/assets/pictures/<?=$media_foto?>" alt="<?=$str_title?>" /></div>
  <? endif; ?>
<? endif ?>

<? if (isset($module_content)): ?>
  <div id="module"><?=$module_content?></div>
<? endif ?>