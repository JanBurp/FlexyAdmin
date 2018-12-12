<?php if ($homemenu): ?>
	<div id="content-home" class="card">
	  <h1 class="card-header">Wat wilt u doen?</h1>
	  <div class="card-body">
	    <?=$homemenu?>
	  </div>
	</div>
<?php endif ?>

<?php if (!empty($plugins)): ?><?=$plugins?><?php endif ?>
