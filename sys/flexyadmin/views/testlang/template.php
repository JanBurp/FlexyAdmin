<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Summary - Translation Tester</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">

    <style>
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            /* Margin bottom by footer height */
            margin-bottom: 60px;
        }

        .alert {
            margin-top: 20px;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 60px;
        }

        .footer .well {
            margin-top: 10px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        ul.list-inline li {
            padding: 5px;
        }

        #testlang-btn-show-error-only {
            margin-left: 20px;
        }

        .table > thead > tr.danger > th,
        .table > tbody > tr.danger > th,
        .table > tfoot > tr.danger > th,
        .table > thead > tr.danger > td,
        .table > tbody > tr.danger > td,
        .table > tfoot > tr.danger > td {
            border-top: 1px solid #dca7a7;
        }

    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>
    <div class="container">

      <div class="row">
        <div class="col-md-12">
          <div class="page-header">
            <div class="pull-right"><a href="http://www.codeigniter.com" target="_blank"><img src="<?php echo base_url('sys/flexyadmin/assets/img/ci-logo.png'); ?>"></a></div>
            <h1><?php echo anchor('_admin/testlang/summary', "Translation Tester"); ?></h1>
          </div>
        </div><!-- col -->
      </div><!-- row -->

<?php $this->load->view($view_name); ?>

    </div><!-- container -->

    <footer class="footer">
      <div class="container">
        <div class="well well-sm">
          <small class="text-muted pull-right">{elapsed_time} sec / {memory_usage}</small>
          <strong>Translation Tester <?php echo TESTLANG_VERSION; ?></strong> &copy; 2015 <a href="mailto:info@aldra.ca?subject=Translation Tester">Alain Rivest</a> - <a href="http://aldra.ca" target="_blank">Aldra.ca</a>
        </div>
      </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

<?php if (isset($js_footer)): ?>
<?php echo $js_footer; ?>
<?php endif; ?>

  </body>
</html>
