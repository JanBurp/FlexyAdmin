<div id="<?=$id?>" class="panel panel-primary doc_page">

  <div class="panel-heading">
    <h2><?=$name?></h2>
  </div>

  <div class="panel-body">
    
    <!-- Nav tabs -->
    <?php if (isset($properties) or isset($methods) or isset($functions)) : ?>
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#<?=$id?>_description" role="tab" data-toggle="tab">Omschrijving</a></li>
      <?php if (isset($properties) and !is_array($properties)):?><li role="presentation"><a href="#<?=$id?>_properties" role="tab" data-toggle="tab">Properties</a></li><?php endif ?>
      <?php if (isset($methods) and !is_array($methods)):?><li role="presentation"><a href="#<?=$id?>_methods" role="tab" data-toggle="tab">Methods</a></li><?php endif ?>
      <?php if (isset($functions) and !is_array($functions)):?><li role="presentation"><a href="#<?=$id?>_functions" role="tab" data-toggle="tab">Functies</a></li><?php endif ?>
    </ul>
    <?php endif ?>

    <!-- Tab panes -->
    <div class="tab-content">

      <div role="tabpanel" class="tab-pane active" id="<?=$id?>_description">
        
        <?php if (isset($path) or (isset($revision) and !empty($revision)) or isset($doc['author'])): ?>
          <div class="well well-sm">
            <dl class="dl-horizontal small">
              <?php if (isset($path)): ?>
                <dt>file:</dt>
                <dd><?=$path?></dd>
              <?php endif ?>
              <?php if (isset($revision) and !empty($revision)): ?>
                <dt>revision:</dt>
                <dd>r<?=$revision?></dd>
              <?php endif ?>
              <?php if (isset($doc['author'])): ?>
                <dt>author(s):</dt>
                <dd><?=strip_tags(implode(' ',$doc['author']))?></dd>
              <?php endif ?>
            </dl>
          </div>
        <?php endif ?>

        <?php if (isset($doc['shortdescription']) and !empty($doc['shortdescription'])): ?><p class="text-primary"><?=$doc['shortdescription']?></p><?php endif ?>
        <?php if (isset($doc['description'])): ?><p><?=$doc['description']?></p><?php endif ?>
      </div>
      
      <?php if (isset($properties)): ?>
      <div role="tabpanel" class="tab-pane" id="<?=$id?>_properties">
        <h2>Properties</h2>
        <?=$properties?>
      </div>
      <?php endif ?>

      <?php if (isset($methods)): ?>
        <div role="tabpanel" class="tab-pane" id="<?=$id?>_methods">
          <h2>Methods</h2>
          <?=$methods?>
        </div>
      <?php endif ?>

      <?php if (isset($functions)): ?>
        <div role="tabpanel" class="tab-pane" id="<?=$id?>_functions">
          <h2>Functies</h2>
          <?=$functions?>
        </div>
      <?php endif ?>
      
      
    </div>

  </div>

</div>
