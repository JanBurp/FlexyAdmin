<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlexyAdmin Update</title>
	  <link rel="stylesheet" href="sys/flexyadmin/assets/css/bootstrap.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
  </head>
  <body>
    
    <div class="container">
    
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h1 class="panel-title"><a href="_update">FlexyAdmin update</a></h1>
        </div>
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Part</th>
                <th>Current version</th>
                <th>Latest version</th>
                <th>Up to date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($updates as $name => $versions): ?>
                <tr>
                  <td><?=$versions['name']?></td>
                  <td><?=$versions['current']?></td>
                  <td><?=$versions['latest']?></td>
                  <td>
                    <a href="?action=<?=$name?>">
                      <?php if ($versions['update']): ?>
                        <span class="glyphicon glyphicon-refresh btn btn-sm btn-warning"></span>
                      <?php else: ?>
                        <span class="glyphicon glyphicon-ok btn btn-sm btn-success"></span>
                      <?php endif ?>
                    </a>
                  </td>
                </tr>
              <?php endforeach ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3">Update all?</th>
                <th>
                  <a href="?action=all">
                    <?php if (!$update_all): ?>
                      <span class="glyphicon glyphicon-refresh btn btn-sm btn-danger"></span>
                    <?php else: ?>
                      <span class="glyphicon glyphicon-ok btn btn-sm btn-success"></span>
                    <?php endif ?>
                  </a>
                </th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
      
      <?php if (!empty($messages)): ?>
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h1 class="panel-title">Performed update actions</h1>
          </div>
          <div class="panel-body">
            <table class="table">
              <thead>
                <tr>
                  <th>Part</th>
                  <th>Performed update action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($messages as $type => $message): ?>
                <tr>
                  <td><?=$type?></td>
                  <td>
                    <?php foreach ($message as $line): ?>
                      <p><span class="glyphicon glyphicon-ok btn btn-xs btn-success"></span>&nbsp;<?=$line?></p>
                    <?php endforeach ?>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif ?>
      
    </div>

  </body>
</html>