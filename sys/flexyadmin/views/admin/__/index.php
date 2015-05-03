<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
  <title>FlexyAdmin Documentation</title>
  
  <script type="text/javascript">
  var root="<?=$root?>";
  </script>
  
  <link rel="stylesheet" href="<?=$root?>assets/css/bootstrap.css" type="text/css" media="screen" title="no title" charset="utf-8">
  <link rel="stylesheet" href="<?=$root?>assets/css/userguide.css" type="text/css" media="screen" title="no title" charset="utf-8">
  
  <script src="<?=$root?>assets/js/jquery-1.11.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$root?>assets/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$root?>assets/js/userguide.js" type="text/javascript" charset="utf-8"></script>

</head>
<body>

  <div id="container" class="container">

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="./index.html"><img src="assets/img/flexyadmin.gif" width="300" height="30" alt="Flexyadmin"></a>
        </div>
        <div class="navbar-text"><a href="http://flexyadmin.com" target="_blank">FlexyAdmin r<?=$revision?></a> &#169; <a href="http://www.jandenbesten.net/"  target="_blank">Jan den Besten</a></div>
        <form id="search_form" class="navbar-form navbar-right" role="search">
          <div id="tipue" class="form-group">
            <input id="search" type="text" class="form-control" placeholder="search">
          </div>
        </form>
      </div>
    </nav>

    <?=$userguide?>
    
  </div>

</body>
</html>