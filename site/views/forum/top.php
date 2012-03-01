<div class="forum_top">
  <? if (isset($config['user_name'])): ?>
  <div class="forum_welcome"><p><?=langp('info_welcome',$config['user_name'])?> <?=langp('info_last_login',$config['user_lastlogin'])?></p></div>
  <? endif ?>
  <?=$config['menu']?>
</div>
