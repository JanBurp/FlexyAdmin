<div class="card grid">
  <h1 class="card-header"><?=$title?></h1>

  <div class="card-block table-responsive">
    <table class="table table-striped table-bordered table-hover table-sm">
      <thead class="">
        <tr>
          <?php foreach ($headers as $header): ?><th><?=$header?></th><?php endforeach ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $id => $row): ?>
          <tr data-id="<?=$id?>">
            <?php foreach ($row as $key => $value): ?>
              <td data-id="<?=$id?>" data-field="<?=$key?>" data-value="<?=strip_tags($value)?>"><?=$value?></td>
            <?php endforeach ?>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
  
  <?php if (isset($info['num_pages']) and $info['num_pages']>1): ?>
  <div class="card-footer text-muted">
    <ul class="pagination">
      <?php for ($i=0; $i < $info['num_pages']; $i++) : ?>
        <li class="page-item <?=($i===$info['page'])?'active':''?>"><a class="page-link" href="#"><?=$i?></a></li>
      <?php endfor ?>
    </ul>
  </div>
  <?php endif ?>
  
</div>
