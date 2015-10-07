<?php
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>

<feed xmlns="http://www.w3.org/2005/Atom">
  <id><?=$feed_url;?></id>
  <title><![CDATA[<?=$feed_name;?>]]></title>
  <updated><?=$updated?></updated>
  <link rel="self" href="<?=$feed_url;?>"/>
  <subtitle><![CDATA[<?=$feed_name;?>]]></subtitle>
  <generator>FlexyAdmin</generator>
<?php foreach($posts as $entry): ?>
  <entry>
    <id><?=site_url($entry['url']);?></id>
    <title><?=xml_convert($entry['title']);?></title>
    <updated><?=$entry['date'];?></updated>
    <link rel="alternate" href="<?=site_url($entry['url']);?>" />
    <summary><![CDATA[<?=$entry['body']; ?>]]></summary>
  </entry>
<?php endforeach; ?>
</feed>