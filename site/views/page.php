<?php if ($show_page): ?>
  <h1><?=ascii_to_entities($str_title);?></h1>
  <div class="text"><?=$txt_text?></div>

  <?php if (!empty($media_foto)) $medias_fotos = $media_foto; ?>
  <?php if (!empty($medias_fotos)) : $medias_fotos=explode('|',$medias_fotos) ?>
  <div class="photo_gallery">
    <?php foreach ($medias_fotos as $media_foto): $title=get_img_title('pictures/'.$media_foto);?>
    <div class="photo">
      <img src="_media/pictures/<?=$media_foto?>" alt="<?=$title?>" />
      <p class="img_title"><?=$title?></p>
    </div>
    <?php endforeach ?>
  </div>
  <?php endif; ?>
  
  
<?php endif ?>

<?php if (isset($module_content)): ?><?=$module_content?><?php endif ?>