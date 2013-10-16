<h1><?=ascii_to_entities($str_title);?></h1>
<div class="text"><?=$txt_text?></div>

<? if (!empty($media_foto)) : $title=get_img_title('pictures/'.$media_foto); ?>
<div class="photo">
  <img src="<?=SITEPATH?>/assets/pictures/<?=$media_foto?>" alt="<?=$title?>" />
  <p class="img_title"><?=$title?></p>
</div>
<? endif; ?>

<? if (isset($comments)): ?>
<?=$comments?>
<? endif ?>
