<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CIUnit Web Interface</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

	<base href="<?=base_url()?>" />

  
  <!-- Le styles -->
  <link type="text/css" rel="stylesheet" href="<?=admin_assets()?>/css/bootstrap.min.css">
  <style type="text/css">
    ul.lev2 {border-left:solid 1px #08C;}
    li {margin-bottom:5px;}
    li li {margin-left:5px;margin-bottom:0px;}
  </style>
  
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>

<body>
  <!-- NAVBAR -->
  <div class="navbar-wrapper">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner"> 
        <?php echo anchor('_unittest', 'CIUnit Framework ' . CIUNIT_VERSION, array('class' => 'brand')); ?>
      </div>
      <!-- /.navbar-inner -->
    </div>
    <!-- /.navbar -->
  </div>
  <!-- /.nabvar-wrapper -->
  
  <div class="container">
    <div class="row-fluid">
      <div class="span12">
        <h3><?php echo $run_failure; ?></h3>
      </div>
    </div>
    
    <hr class="divider" />
    
    <footer>
      <p class="pull-right">
        <a href="#">Back to top</a>
      </p>
      <p>&copy; 2013 CIUnit.</p>
    </footer>
    
  </div>
  <!-- /.container -->
</body>

</html>
