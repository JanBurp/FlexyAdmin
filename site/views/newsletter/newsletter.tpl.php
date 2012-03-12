<? foreach ($pages as $id => $page): ?>
<h1><?=$page['str_title']?></h1>
<?=$page['txt_text']?>
<p><a href="<?=$page['uri']?>">lees meer</a></p>
<hr/>
<? endforeach ?>

<? if (isset($unsubmit)): ?>
Afmelden: Klik <a href="<?=$unsubmit?>">hier</a> als u zich wilt afmelden van de nieuwsbrief<br />  
<? endif ?>

