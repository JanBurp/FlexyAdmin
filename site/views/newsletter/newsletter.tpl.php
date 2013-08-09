<table width="600">
  <!-- HEADER -->
  <tr>
    <td colspan="3" width="600" height="200"><img src="site/assets/img/nieuwsbrief-header.jpg" width="600" height="196" alt="DANTHE NIEUWSBRIEF - participatie in besluitvorming"></td>
  </tr>
  
  <!-- CONTENT -->
  <tr>
    <td width="20">&nbsp;</td>
    <td width="560">
      <? $i=0; foreach ($items as $id => $item): $i++; ?>
      <img style="float:left;" src="site/assets/img/ikoontje-blauw-<?=$i?>.jpg" width="60" alt="$item['str_title']?>">
      <h1><br><?=$item['str_title']?><br></h1>
      <?=$item['txt_text']?><? if (isset($item['uri'])): ?><a href="<?=$item['uri']?>">lees meer</a><? endif ?>
      <br><br>
      <? endforeach ?>
    </td>
    <td width="20">&nbsp;</td>
  </tr>
  
  <!-- FOOTER -->
  <tr>
    <td width="20">&nbsp;</td>
    <td width="560" style="padding:20px;">
      <hr/>
      <? if (isset($unsubmit)): ?>
      <p>Afmelden: Klik <a href="<?=$unsubmit?>">hier</a> als u zich wilt afmelden van de nieuwsbrief<br /></p>
      <? endif ?>
    </td>
    <td width="20">&nbsp;</td>
  </tr>
</table>