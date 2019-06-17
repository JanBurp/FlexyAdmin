<?php switch ($this->site['framework']) {
  case 'default':
  case 'bootstrap':
  case 'bootstrap3': ?>
    <ul class="nav navbar-nav lev<?=$lev?> <?=$sub?>"><?=$menu?></ul>
  <?php break;

  case 'bootstrap4': ?>
    <ul class="<?=$lev==1?'navbar-nav':''?> lev<?=$lev?> <?=$sub?>"><?=$menu?></ul>
  <?php break;

  case 'bootstrap4vue': ?>
    <ul class="nav navbar-nav lev<?=$lev?> <?=$sub?>"><?=$menu?></ul>
  <?php break;
}
?>
