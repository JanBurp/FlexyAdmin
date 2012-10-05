<? if (!empty($commonHelp)): ?>
<h1>Help</h1>
<div class="content">
  <?=$commonHelp?>
</div>
<? endif ?>


<div id="subitems">
  <? if (!empty($help)): ?>
  <?=$help?>
  <? endif ?>

  <!-- <? if (!empty($specificHelp)): ?>
  <h1>Help per onderdeel</h1>
  <div class="content">
    <p>Deze help teksten krijg je ook te zien als je met de muis op de naam van een onderdeel gaat staan en even wacht.<br/><br/></p>
    <?=$specificHelp?>
  </div>
  <? endif ?> -->

  <!-- <h1>Welke browser?</h1>
  <div class="content">
    <p>Het admin gedeelte van FlexyAdmin werkt op moderne browsers.<br/>
    Mocht je problemen ondervinden, gebruik dan de gratis en veilige <a href="http://www.mozilla.org/firefox">Firefox</a> browser.</p>
  </div> -->

</div>

