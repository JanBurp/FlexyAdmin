<h1><?=$title?></h1>

<? if (isset($messages)): ?>
  <? foreach ($messages as $message): ?>
  <p><?=$message?></p>
  <? endforeach ?>
<? endif ?>

<? if (isset($content)): ?><?=$content?><? endif ?>
