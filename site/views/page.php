<h1><?=ascii_to_entities($str_title);?></h1>
<div class="text"><?=$txt_text?></div>

<? if (!empty($media_foto)) : ?>
	<div class="photo"><img src="site/assets/pictures/<?=$media_foto?>" alt="<?=$str_title?>"</div>
<? endif; ?>


