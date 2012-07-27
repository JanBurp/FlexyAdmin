<div>
  <h1 class="doc_file"><?=$file?></h1>
  <? if ($CIparent): ?>
    Dit is een uitbreiding op CodeIgniter's <a href="<?=$CIparent?>" target="_blank"><?=str_replace('MY_','',$file)?></a>.
  <? endif ?>
  
  <p class="doc_description"><?=$shortdescription?></p>
  <p class="doc_description"><?=$description?></p>

  <div class="doc_properties">
  <h1>Properties</h1><?=$properties?>
  </div>
  <div class="doc_functions">
  <h1>Methods</h1><?=$methods?>
  </div>
</div>