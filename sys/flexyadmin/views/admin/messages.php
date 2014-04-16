<?php if ($messages): ?>
<div id="messages" class="messages">
  <?php foreach ($messages as $message): ?>
    <p class="message"><?=$message?></p>
  <?php endforeach ?>
</div>
<?php endif ?>

<?php if ($errors): ?>
<div id="errors" class="messages">
  <?php foreach ($errors as $error): ?>
    <p class="error"><?=$error?></p>
  <?php endforeach ?>
</div>
<?php endif ?>
