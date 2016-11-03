<div class="card form">
  <div class="card-header">
    <h1><?=$title?></h1>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-sm btn-danger">Annuleer<span class="fa fa-close"></span></button>
      <button type="button" class="btn btn-sm btn-warning">Bewaar<span class="fa fa-save"></span></button>
      <button type="button" class="btn btn-sm btn-info">Invoeren<span class="fa fa-check"></span></button>
    </div>
  </div>

  <div class="card-block">
    
    <tabs navStyle="tabs">
      <?php $active=true; foreach ($fieldsets as $fieldset => $fieldkeys): ?>
        <tab header="<?=$fieldset?>">
          <?php foreach ($fieldkeys as $field): ?>
            <div class="form-group row">
              <label class="col-xs-2 col-form-label" for="<?=$field?>"><?=$fields[$field]['name']?></label>
              <div class="col-xs-10"><input type="text" class="form-control" id="<?=$field?>" value="<?=$data[$field]?>" placeholder=""></div>
            </div>
          <?php endforeach ?>
        </tab>
      <?php $active=false; endforeach ?>
    </tabs>

  </div>
</div>
