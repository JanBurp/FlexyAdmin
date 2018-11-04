<h1 class="text-primary">Bulkupload result:</h1>

<h2>Added <?=count($added)?> files, and <?=count($errors)?> errors.</h2>

<?php if (count($errors)>0): ?>
	<h3 class="text-danger">Errors:</h3>
	<ul>
		<?php foreach ($errors as $file): ?>
			<li><?=$file?></li>
		<?php endforeach ?>
	</ul>	
<?php endif ?>

<h2>Added:</h2>
<ul>
	<?php foreach ($added as $file): ?>
		<li><?=$file?></li>
	<?php endforeach ?>
</ul>
