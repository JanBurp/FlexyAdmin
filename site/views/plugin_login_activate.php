<h1><?=$title?></h1>

<? if (!empty($messages)): ?><? foreach ($messages as $message): ?>
  <p><?=$message?></p>  
<? endforeach ?><? endif ?>

<?=$grid?>