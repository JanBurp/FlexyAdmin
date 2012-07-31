var tipuedrop = { "pages":[
<? foreach ($data as $s): ?>
  { "title": "<?=$s['title']?>", "thumb": "", "text": "<?=$s['text']?>", "tags": "", "loc": (root+"<?=$s['loc']?>") },
<? endforeach ?>
]};