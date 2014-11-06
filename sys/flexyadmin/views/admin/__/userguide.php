<div id="sitemap">

  <div class="row row1">
    
    <?php $nr=-1; foreach ($items as $head => $sub): $nr++; 
      if ($nr%4==0) {
        ?></div><div class="row row2"><?
      }
      ?>
      <div class="col-sm-3">
        <div class="panel panel-primary">
          <div class="panel-heading"><?=str_replace('_',' ',$head)?></div>
          <div class="list-group">
          <?php foreach ($sub as $name => $html): ?>
            <a class="list-group-item btn btn-default" href="#section_<?=safe_string($name)?>"><?=$name?></a></li>
          <?php endforeach ?>
          </div>
        </div>
      </div>
    <?php endforeach ?>
    
  </div>
  
</div>



<div class="row">

  <!-- <div id="nav" class="col-sm-3">
    <?php foreach ($items as $head => $sub): ?>
      <?php if (!empty($sub)): ?>
      <div class="panel panel-info">
        <div class="panel-heading collapsed" data-toggle="collapse" data-target="#nav_<?=safe_string($head)?>">
          <span class="glyphicon glyphicon-chevron-right collapsed"></span><span class="glyphicon glyphicon-chevron-down expanded"></span>
          <?=$head?>
        </div>
        <div id="nav_<?=safe_string($head)?>" class="list-group collapse">
        <?php foreach ($sub as $name => $html): ?>
          <a class="list-group-item btn btn-default" href="#section_<?=safe_string($name)?>"><?=$name?></a>
        <?php endforeach ?>
        </div>
      </div>
      <?php endif ?>
    <?php endforeach ?>
  </div> -->

  <div id="content" class="col-sm-12">
    <?php foreach ($items as $head => $sub): ?>
      <?php if (!empty($sub)): ?>
        <h1><?=$head?></h1>
        <?php foreach ($sub as $name => $html): ?>
          <div id="section_<?=safe_string($name)?>" class="section">
            <?=$html?>
          </div>
        <?php endforeach ?>
      <?php endif ?>
    <?php endforeach ?>
  </div>
  
</div>


