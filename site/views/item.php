<div class="item">

	<? if (!empty($media_foto)): ?>
	<div class="picture"><?=img($map."/".$media_foto); ?></div>
	<? endif; ?>

	<div class="text">
		<? if (!empty($dat_date)): ?>
		<div class="date"><?=$dat_date;?></div>
		<? endif; ?>

		<h2><?=$str_title;?></h2>
		<?=$txt_tekst?>
	</div>

</div>
