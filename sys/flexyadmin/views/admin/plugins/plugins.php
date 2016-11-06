<div class="card">
  <h1 class="card-header"><?=$title?></h1>
  <div class="card-block">
    <table class="table table-hover table-sm">
      <?php foreach ($plugins as $plugin): ?>
        <tr>
          <td><a href="<?=site_url().$plugin['uri']?>"><b><?=strtoupper($plugin['name'])?></b></td>
          <td>
            <?php if (is_array($plugin['doc'])): ?>
              <b><?=$plugin['doc']['short']?></b> <?=$plugin['doc']['long']?>
            <?php else: ?>
              <?=$plugin['doc']?>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach ?>
    </table>
  </div>
</div>


