<accordion>
  <?php $show=true; foreach ($items as $uri => $item): ?>
    <panel header="<?=$item['title']?>" <?php if ($show): ?>  <?php endif ?>>
      <?=$item['content']?>
    </panel>
  <?php $show=false; endforeach ?>
</accordion>

