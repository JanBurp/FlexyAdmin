<div id="breadcrumb">
  <ul>
    <?php foreach ($segments as $key => $segment): ?>
      <li class="<?=$segment['class']?>"><a href="<?=$segment['uri']?>"><?=$segment['name']?></a><?php if (!$segment['last']): ?> &gt; <?php endif ?></li>
    <?php endforeach ?>
  </ul>
</div>
