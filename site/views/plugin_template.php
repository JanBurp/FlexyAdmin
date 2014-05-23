<h1><?=$title?></h1>

<?php if (!empty($messages)): ?><?php foreach ($messages as $message): ?>
  <p><?=$message?></p>  
<?php endforeach ?><?php endif ?>
