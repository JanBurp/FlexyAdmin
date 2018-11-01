<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?>" <?=$attr?>>
	<?php if (substr($uri,0,4)==='load'): ?>
	  <a href="_admin/<?=$uri?>" class="rawload nav-link <?=$current?> <?=$class?>" title="<?=$title?>">
	  	<?php if (isset($icon) and !empty($icon)): ?><span class="fa fa-<?=$icon?>"></span><?php endif ?>
	  	<?=$title?>
	  </a>
	<?php else: ?>
		<router-link to="/<?=$uri?>" class="nav-link <?=$current?> <?=$class?>" title="<?=$title?>">
			<?php if (isset($icon) and !empty($icon)): ?><span class="fa fa-<?=$icon?>"></span><?php endif ?>
			<?=$title?>
		</router-link>
	<?php endif	?>
  <?=$submenu?>
</li>