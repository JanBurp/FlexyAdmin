<div class="nav-item">
	<?php if (substr($uri,0,4)==='load'): ?>
		<a href="_admin/<?=$uri?>"><flexy-button icon="<?=$icon?>" class="rawload btn-<?=$class?>"></flexy-button><h1 class="nav-title text-<?=$class?>"><?=$title?></h1></a>	
	<?php else: ?>
  	<router-link to="/<?=$uri?>"><flexy-button icon="<?=$icon?>" class="btn-<?=$class?>"></flexy-button><h1 class="nav-title text-<?=$class?>"><?=$title?></h1></router-link>
	<?php endif ?>
</div>