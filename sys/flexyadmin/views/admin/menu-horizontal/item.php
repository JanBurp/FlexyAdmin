<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?> <?=$class?>" <?=$attr?>>
	<?php if (!empty($uri)): ?>
		<router-link to="/<?=$uri?>" class="nav-link <?=$current?>" <?=isset($html)?$html:'';?>>
			<span class="fa fa-<?=$icon?>"></span>
			<?php if ($active_icon): ?><span class="show-when-active fa fa-<?=$active_icon?>"></span><?php endif ?>
			<span class="nav-title"><?=$title?></span>
		</router-link><?=$submenu?>
	<?php else: ?>
		<a href="#" class="nav-link <?=$current?>" <?=isset($html)?$html:'';?>>
			<span class="fa fa-<?=$icon?>"></span>
			<?php if ($active_icon): ?><span class="show-when-active fa fa-<?=$active_icon?>"></span><?php endif ?>
			<span class="nav-title"><?=$title?></span>
		</a><?=$submenu?>
	<?php endif ?>
</li>