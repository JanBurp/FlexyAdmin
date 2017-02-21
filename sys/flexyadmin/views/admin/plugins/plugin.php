<div class="card">
  <h1 class="card-header"><?=$title?></h1>
  <div class="card-block">
    <?php if (isset($messages)): ?>
      <?php foreach ($messages as $message): ?>
      <p><?=$message?></p>
      <?php endforeach ?>
    <?php endif ?>
    <?php if (isset($content)): ?><?=$content?><?php endif ?>
  </div>
</div>


