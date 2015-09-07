      <div class="row">
        <div class="col-md-12">

          <h2>Available languages <a href="https://github.com/bcit-ci/codeigniter3-translations" target="_blank"><img src="<?php echo base_url('sys/flexyadmin/assets/img/github.png'); ?>"></a></h2>
<?php echo ul($languages, array('class' => 'list-inline')); ?>

<?php echo heading(ucfirst($idiom), 2); ?>

          <ul class="nav nav-pills">
<?php foreach ($lang_text as $i => $file) : ?>
            <li role="presentation"><a href="#file-<?php echo $i; ?>"><?php echo $file['filename']; ?></a></li>
<?php endforeach; ?>
          </ul>

<?php if ($nb_error > 0) : ?>
          <div class="alert alert-danger" role="alert">
            <strong>Warning!</strong> <?php echo $nb_error; ?> error found!
            <button id="testlang-btn-show-error-only" class="btn btn-danger" type="button">Show error only / All</button>
          </div>
<?php else : ?>
          <div class="alert alert-success" role="alert"><strong>Congratulation!</strong> No error found!</div>
<?php endif; ?>

<?php foreach ($lang_text as $i => $file) : ?>

<?php if ($file['nb_error'] > 0) : ?>
          <div class="panel panel-primary testlang-file-error">
<?php else : ?>
          <div class="panel panel-primary testlang-file-no-error">
<?php endif; ?>

            <div class="panel-heading">
              <div class="pull-right">
                <h3 class="panel-title"><a href="#" id="file-<?php echo $i; ?>"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span> Back to top</a></h3>
              </div>
              <h3 class="panel-title"><?php echo "{$file['directory']}/language/$idiom/<strong>{$file['filename']}</strong>"; ?></h3>
            </div>

            <table class="table table-condensed table-hover">
              <tr>
                <th style="width:40px"><p class="text-right">#</p></th>
                <th style="width:270px">Key</th>
                <th>Text</th>
              </tr>
<?php
            foreach ($file['data'] as $i => $data)
            {
                $error = FALSE;
                $text = '';

                // Translated text
                if (isset($data['text_error']))
                {
                    $error = TRUE;
                    $text = "<strong>*** {$data['text_error']} ***</strong>";
                }
                else if (isset($data['text']))
                {
                    $text = "<span class=\"text-primary\">{$data['text']}</span>";
                }

                // Default text
                if (isset($data['default_text_error']))
                {
                    $error = TRUE;
                    $text .= "<br><strong>*** {$data['default_text_error']} ***</strong>";
                }
                else if (isset($data['default_text']))
                {
                    $text .= "<br><span class=\"text-muted\"><em>{$data['default_text']}</em></span>";
                }

                $no = $i + 1;
                $tr = ($error) ? '<tr class="testlang-text-error danger">' : '<tr class="testlang-text-no-error">';

                echo "$tr<td><p class=\"text-right\">$no</p></td><td>{$data['key']}</td><td>$text</td></tr>\n";
            }
?>
            </table>

          </div>

<?php endforeach; ?>

        </div><!-- col -->
      </div><!-- row -->
