<div id="plugin_db_scheme">
  <h1>DB SCHEME</h1>

  <h1>Statistics</h1>

  <table>
    <?php foreach ($statistics as $key => $value): ?>
      <tr><td><?=$key?></td><td><?=$value?></td></tr>
    <?php endforeach ?>
    <tr>
  </table>


  <h1>Tables</h1>

  <?php foreach ($tables as $table): ?>
  <table>
    <thead>
      <tr><td colspan="100%"><b><?=$table['name']?></b></td></tr>
    </thead>
    <tbody>
      <?php foreach ($table['fields']['fields'] as $key): ?>
        <tr><td><?=$key?></td></tr>
      <?php endforeach ?>
      <?php foreach ($table['fields']['foreign_keys'] as $key): ?>
        <tr><td class="foreign_key" align="right"><?=$key?> =></td></tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php endforeach ?>
  
</div>