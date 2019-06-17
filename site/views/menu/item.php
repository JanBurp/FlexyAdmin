<?php switch ($this->site['framework']) {
  case 'default':
  case 'bootstrap':
  case 'bootstrap3': ?>
    <li class="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>" <?=$attr?>>
      <?php if (!$submenu): ?>
        <a href="<?=site_url($uri)?>" class="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?> <?=(!$clickable?'disabled':'')?>"><?=$title?></a>
      <?php else: ?>
        <a href="<?=site_url($uri)?>" class="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?>">
          <?=$title?>
        </a>
        <a href="" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-chevron-down"></span></a>
        <?=$submenu?>
       <?php endif ?>
    </li>
  <?php break;

  case 'bootstrap4': ?>
    <li class="nav-item lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>" <?=$attr?>>
      <a href="<?=site_url($uri)?>" class="nav-link lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?> <?=(!$clickable?'disabled':'')?>"><?=$title?></a>
      <?php if ($submenu): ?>
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"></a>
      <?=$submenu?>
      <?php endif ?>
    </li>
  <?php break;

  case 'bootstrap4vue': ?>
    <dropdown href="<?=site_url($uri)?>" title="<?=$title?>" dropdown="<?=$sub?>" attr="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>">
      <?=$submenu?>
    </dropdown>
  <?php break;
}
?>
