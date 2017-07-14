<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?>" <?=$attr?>>
  <router-link to="/<?=$uri?>" class="nav-link <?=$current?> <?=$class?>" title="<?=$title?>">
  	<?php if (isset($icon) and !empty($icon)): ?><span class="fa fa-<?=$icon?>"></span><?php endif ?>
  	<?=$title?>
  </router-link>
  <?=$submenu?>
</li>