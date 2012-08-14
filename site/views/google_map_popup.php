<h1><?=$str_title?></h1>
<p>
<?=$str_address?><br />
<? if (isset($txt_text)): ?><?=$txt_text?><? endif ?>
<? if (isset($url_url)): ?><a href="<?=$url_url?>" target="_blank"><?=str_replace('http://','',$url_url)?></a><? endif ?>
</p>
