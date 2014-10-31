<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf8" />
  <title>FlexyAdmin Documentation</title>
  
  <script type="text/javascript">
  var root="<?=$root?>";
  </script>
  
  <link rel="stylesheet" href="<?=$root?>assets/css/userguide.css" type="text/css" media="screen" title="no title" charset="utf-8">
  
  <script src="<?=$root?>assets/js/zepto.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$root?>assets/js/jquery-1.11.1.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$root?>assets/js/jquery.waterfall.js" type="text/javascript" charset="utf-8"></script>

  <script src="<?=$root?>assets/js/toc.js" type="text/javascript" charset="utf-8"></script>
  <script src="<?=$root?>assets/js/doc.js" type="text/javascript" charset="utf-8"></script>
  
  <script type="text/javascript" src="<?=$root?>assets/tipuedrop/tipuedrop_set.js"></script>
  <script type="text/javascript" src="<?=$root?>assets/tipuedrop/data.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$root?>assets/tipuedrop/tipuedrop.css">
  <script type="text/javascript" src="<?=$root?>assets/tipuedrop/tipuedrop.js"></script>
</head>
<body>

  <!-- START NAVIGATION -->
  <div id="masthead">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
      <tr>
        <td width="40%"><a href="<?=$root?>index.html"><h1 class="logo">FlexyAdmin</h1></a></td>
        <td width="40%" valign="bottom"><a href="http://flexyadmin.com" target="_blank">FlexyAdmin r<?=$revision?></a> &#169; <a href="http://www.jandenbesten.net/"  target="_blank">Jan den Besten</a></td>
        <td id="searchbox" width="20%">
          <div id="tipue"><input type="text" id="tipue_drop_input" autocomplete="off" placeholder="search (focus at keypress)"></div>
          <div id="tipue_drop_content"></div>
        </td>
      </tr>
    </table>
  </div>
  <div id="nav3">
    <table cellpadding="0" cellspacing="0" border="0" style="width:100%">
      <tr>
        <td align="left" width="25%"><a class="prev" href="">Vorige</a></td>
        <td align="right" width="25%"><a class="next" href="">Volgende</a></td>
      </tr>
    </table>
  </div>

  <!-- END NAVIGATION -->

  <br clear="all" />


  <!-- START CONTENT -->
  <div id="content"><?=$content?></div>
  <!-- END CONTENT -->

</body>
</html>