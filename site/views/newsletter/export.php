<h1><?=$title?></h1>

<? if (!empty($messages)): ?><? foreach ($messages as $message): ?>
  <p><?=$message?></p>  
<? endforeach ?><? endif ?>

<p><textarea style="width:590px" rows="20"><?=$adresses?></textarea></p>