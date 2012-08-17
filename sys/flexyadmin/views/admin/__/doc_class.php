<div id="<?=str_replace(' ','_',$file)?>" class="doc_page">

  <? if (isset($parent) and $parent): $link=(substr($parent,0,2)!='CI' and substr($parent,0,2)!='MY'); ?>
  <p class="doc_parent">
    <? if ($link): ?><a href="../libraries/<?=$parent?>.html"><? endif ?>
    <?=$parent?>
    <? if ($link): ?></a><? endif ?>
     -&gt; 
  </p>
  <? endif ?>
  <h1 class="doc_file"><?=$file?></h1>
  <p class="doc_path"><?=$path?></p>
  
  <p class="doc_description"><b><?=$shortdescription?></b></p>
  <p class="doc_description"><?=$description?></p>
  
  <? if (!empty($properties)): ?>
  <h1>Properties</h1>
  <div class="doc_properties accordion">
  <?=$properties?>
  <br />
  </div>
  <? endif ?>

  <? if (!empty($methods)): ?>
  <h1>Methods</h1>
  <div class="doc_functions accordion">
  <?=$methods?>
  </div>
  <? endif ?>

</div>