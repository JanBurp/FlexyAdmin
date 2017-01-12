<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="nav-item lev<?=$lev?> pos<?=$pos?>" <?=$attr?>>
  <a href="<?=site_url($uri)?>" class="nav-link <?=$current?> <?=($clickable?'':'nonClickable')?>"><?php if (isset($icon) and !empty($icon)): ?><span class="fa fa-<?=$icon?>"></span><?php endif ?><span class="nav-title"><?=$title?></span></a><?=$submenu?>
</li>