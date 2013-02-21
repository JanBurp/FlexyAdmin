<? foreach ($items as $id => $item): ?>
<h1><?=$item['str_title']?></h1>
<?=$item['txt_text']?>
<? if (isset($item['uri'])): ?>
<p><a href="<?=$item['uri']?>">lees meer</a></p>  
<? endif ?>
<hr/>
<? endforeach ?>

<? if (isset($unsubmit)): ?>
Afmelden: Klik <a href="<?=$unsubmit?>">hier</a> als u zich wilt afmelden van de nieuwsbrief<br />  
<? endif ?>

