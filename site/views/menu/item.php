<li class="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$sub?> <?=$class_uri?> <?=$class?> <?=$current?>" <?=$attr?>>
  <?php if (!$submenu): ?>
  	<a href="<?=site_url($uri)?>" class="lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?> <?=(!$clickable?'disabled':'')?>"><?=$title?></a>
  <?php else: ?>
	  <a href="<?=site_url($uri)?>" class="dropdown-toggle disabled lev<?=$lev?> pos<?=$pos?> _pos<?=$_pos?> <?=$order?> <?=$class_uri?> <?=$current?>"><?=$title?></a>
	  <a class="dropdown-toggle dropdown-caret" data-toggle="dropdown" aria-expanded="false"><b class="caret"></b></a>
	  <?=$submenu?>
	  <?php endif ?>
</li>