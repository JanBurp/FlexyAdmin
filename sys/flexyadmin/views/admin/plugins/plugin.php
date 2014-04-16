<h1><?=$title?></h1>

<?php if (isset($messages)): ?>
  <?php foreach ($messages as $message): ?>
  <p><?=$message?></p>
  <?php endforeach ?>
<?php endif ?>

<?php if (isset($content)): ?><?=$content?><?php endif ?>
