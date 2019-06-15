<li class="nav-item lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>" <?=$attr?>>
  <a href="<?=site_url($uri)?>" class="nav-link lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?> <?=(!$clickable?'disabled':'')?>"><?=$title?></a>
  <?php if ($submenu): ?>
  <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"></a>
  <?=$submenu?>
  <?php endif ?>
</li>
