<? if ($messages): ?>
<div id="messages" class="messages">
  <? foreach ($messages as $message): ?>
    <p class="message"><?=$message?></p>
  <? endforeach ?>
</div>
<? endif ?>

<? if ($errors): ?>
<div id="errors" class="messages">
  <? foreach ($errors as $error): ?>
    <p class="error"><?=$error?></p>
  <? endforeach ?>
</div>
<? endif ?>
