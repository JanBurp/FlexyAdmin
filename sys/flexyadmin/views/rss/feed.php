<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<rss version="2.0"	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:content="http://purl.org/rss/1.0/modules/content/">

	<channel>
		<title><?=$feed_name;?></title>

		<link><?=$feed_url;?></link>
		<description><?=$page_description;?></description>
		<dc:language><?=$page_language;?></dc:language>
		<dc:creator><?=$creator_email;?></dc:creator>

		<dc:rights>Copyright <?=gmdate("Y", time());?></dc:rights>
		<admin:generatorAgent rdf:resource="http://www.flexyadmin.com/" />

		<? foreach($posts as $entry): ?>
			<item>

				<title><?=xml_convert($entry['title']);?></title>
				<link><?=site_url($entry['url']);?></link>
				<guid><?=site_url($entry['url']);?></guid>
				<description><![CDATA[<?=$entry['body']; ?>]]></description>
				<pubDate><?=$entry['date'];?></pubDate>
				
			</item>
		<? endforeach; ?>

	</channel>
</rss>