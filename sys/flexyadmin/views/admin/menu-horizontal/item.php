<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?> <?=$class?>" <?=$attr?>>
  <router-link to="/<?=$uri?>" class="nav-link <?=$current?>" <?=isset($html)?$html:'';?>>
  	<span class="fa fa-<?=$icon?> <?=(isset($active_icon)?'hide-when-active':'')?>"></span>
  	<?php if ($active_icon): ?><span class="show-when-active fa fa-<?=$active_icon?>"></span><?php endif ?>
  	<span class="nav-title"><?=$title?></span>
  </router-link><?=$submenu?>
</li>