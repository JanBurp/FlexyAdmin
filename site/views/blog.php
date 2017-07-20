<?php foreach ($items as $item): ?>
  <div class="blog-item">
    <h1><?=ascii_to_entities($item['str_title']);?></h1>
    <div class="date"><?=strftime('%a %e %B %Y',date_to_unix($item['dat_date']))?></div>
    <div class="text"><?=$item['txt_text']?></div>
  </div>
<?php endforeach ?>
