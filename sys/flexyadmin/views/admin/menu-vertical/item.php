<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?> <?=$current?>" <?=$attr?>>
  <a href="<?=site_url($uri)?>" class="nav-link <?=$class?> <?=($clickable?'':'nonClickable')?>"><?php if (isset($icon) and !empty($icon)): ?><span class="fa fa-<?=$icon?>"></span><?php endif ?><?=$title?></a><?=$submenu?>
</li>