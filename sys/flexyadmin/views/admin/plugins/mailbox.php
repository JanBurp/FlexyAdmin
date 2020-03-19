<p>
  <a class="btn btn-outline-primary" href="_admin/load/plugin/mailbox/export" target="_blank">Exporteer alle emails als .csv/excel bestand</a>
</p>
<p>
  <?=count($emails)?> verzonden emails in afgelopen drie maanden.
</p>

<table class="table table-bordered table-hover table-sm">
  <thead class="bg-primary">
    <tr>
      <th>&nbsp;</th>
      <th>Datum</th>
      <th>Afzender</th>
      <th>Onderwerp</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($emails as $key=>$email): ?>
    <tr>
      <td><a href="_admin/plugin/mailbox/show/<?=$key?>"><span class="fa fa-eye"></span></a></td>
      <td><?=$email['date']?></td>
      <td><b><?=$email['reply-to']?></b></td>
      <td><?=$email['subject']?></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>

