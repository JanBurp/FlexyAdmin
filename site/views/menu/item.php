<li id="menu_lev<?=$lev?>_pos<?=$pos?>" class="lev<?=$lev?> pos<?=$pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>" <?=$attr?>>
  <?php if (!$submenu): ?>
  <a href="<?=site_url($uri)?>" class="lev<?=$lev?> pos<?=$pos?> <?=$order?> <?=$class_uri?> <?=$current?> <?=(!$clickable?'disabled':'')?>"><?=$title?></a>
  <?php else: ?>
  <a href="<?=site_url($uri)?>" class="dropdown-toggle <?=($framework=='bootstrap'?'disabled':'')?> lev<?=$lev?> pos<?=$pos?> <?=$order?> <?=$class_uri?> <?=$current?>"><?=$title?></a>
  <?php if ($framework=='bootstrap'): ?><a class="dropdown-toggle dropdown-caret" data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a><?php endif ?>
  <?=$submenu?>
  <?php endif ?>
</li>