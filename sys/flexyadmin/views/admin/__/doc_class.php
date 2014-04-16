<div id="<?=str_replace(' ','_',$file)?>" class="doc_page">

  <?php if (isset($parent) and $parent): $link=(substr($parent,0,2)!='CI' and substr($parent,0,2)!='MY'); ?>
  <p class="doc_parent">
    <?php if ($link): ?><a href="../libraries/<?=$parent?>.html"><?php endif ?>
    <?=$parent?>
    <?php if ($link): ?></a><?php endif ?>
     -&gt; 
  </p>
  <?php endif ?>
  <h1 class="doc_file"><?=$file?></h1>
  <p class="doc_path"><?=$path?></p>

  <?php if (isset($doc['author'])): ?>
  <p class="doc_author"><?=implode(' ',$doc['author'])?></p>
  <?php endif ?>
  
  
  <p class="doc_description"><b><?=$shortdescription?></b></p>
  <p class="doc_description"><?=$description?></p>
  
  
  <?php if (!empty($properties)): ?>
  <h1>Properties</h1>
  <div class="doc_properties accordion">
  <?=$properties?>
  <br />
  </div>
  <?php endif ?>

  <?php if (!empty($methods)): ?>
  <h1>Methods</h1>
  <div class="doc_functions accordion">
  <?=$methods?>
  </div>
  <?php endif ?>

</div>