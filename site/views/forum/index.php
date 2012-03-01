<? if (!empty($recent)): ?>
  <div class="forum_recents">
  <h2><?=lang('index_active_threads')?></h2>
  <? foreach ($recent as $id => $message): ?>
    <p>
      <span class="right stats"><?=strftime( $config['datetime_format'] ,mysql_to_unix($message['tme_date']))?></span>
      <a class="max_left" href="<?=$config['module_uri']?>/<?=$message['full_uri']?>"><?=$message['full_title']?></a>
    </p>
  <? endforeach ?>
  </div>
<? endif ?>

<? foreach ($index as $id_categorie => $categorie): ?>
  <div class="forum_categorie">
    <h2><?=$categorie['str_title']?></h2>
    <? foreach ($categorie['threads'] as $id_thread => $thread): ?>
      <p>
        <span class="right stats"><? if (isset($config['user_name']) and $thread['new_messages_count']>0): ?><?=$thread['new_messages_count'].' '.lang('index_recent')?>, <? endif; ?><?=$thread['messages_count'].' '.lang('index_total')?></span>
        <a class="max_left" href="<?=$config['module_uri']?>/<?=$categorie['uri']?>/<?=$thread['uri']?>"><?=$thread['str_title']?></a>
      </p>
    <? endforeach ?>
  </div>
<? endforeach ?>