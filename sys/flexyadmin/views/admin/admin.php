<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />

	<title>FlexyAdmin - <?=$str_title?></title>
	<base href="<?=base_url()?>" />
  
  <link rel="stylesheet" href="<?=admin_assets()?>css/flexyadmin.min.css" type="text/css" />
</head>

<body>

<div id="main" class="container-fluid">
  
  <div id="header" class="navbar navbar-fixed-top">
    <div class="navbar-text"><flexy-blocks href="admin" text="<?=$str_title?>"/></div>
    <?=$headermenu?>
  </div>
  
  <div class="flexy-alerts"></div>


  <div id="content" class="row">
    <div id="flexy-menu-side" class="col-sm-2"><?=$sidemenu?></div>

    <div id="main" class="col-sm-10">
      <div class="card">
        <h1 class="card-header">Main</h1>
        <div class="card-block">
          <h2>Lorem ipsum dolor sit amet</h2>
          <p>Consectetur adipiscing elit. Vivamus in augue ac justo posuere luctus sodales vel justo. Integer blandit, quam id porttitor consequat, lorem libero bibendum ipsum, non auctor sem ipsum eu mauris. <b>Vestibulum condimentum,</b> lectus sed aliquam rutrum, est velit pellentesque mauris, sed mattis sapien ante vitae enim. Quisque cursus facilisis molestie. Sed rhoncus lacus ac nunc interdum in laoreet mi rhoncus. Suspendisse ultrices fringilla felis, in porta mi pretium ut. Nunc nisl nulla, varius in lobortis a, dictum a purus. Sed consequat felis ut erat lobortis hendrerit. Donec bibendum lorem lorem. Fusce suscipit sapien id lorem mollis vel placerat nunc congue. Aenean non nunc tortor. <i>Curabitur rhoncus neque eget nulla adipiscing euismod.</i></p>
          <h2>Duis tincidunt sollicitudin convallis</h2>
          <p>Quisque nibh tortor, blandit a mollis vitae, euismod non nulla. Duis dui erat, interdum sit amet porttitor a, porttitor nec augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed quis porta turpis. Suspendisse nec mi enim, ut fringilla tellus. Nunc sollicitudin justo at leo tempus eu fringilla nisl tempus. Sed id tellus non eros tristique vehicula. Quisque sollicitudin augue id velit euismod interdum. Proin lobortis ornare magna in facilisis. Nulla vestibulum ultricies dui ut fringilla. Duis eu ante in lorem pellentesque bibendum. Praesent id velit vel nulla ullamcorper adipiscing quis quis tellus. Integer nec augue quis felis dapibus imperdiet ac et nibh.</p>
        </div>
      </div>
    </div>
    
  </div>
  
  <div id="footer" class="navbar navbar-fixed-bottom">
    <div class="navbar-text"><flexy-blocks href="admin" text="TokPit"/></div>
    <div class="navbar-text"><span class="flexy-block btn btn-outline-danger text-lowercase"><?=$version?></span></div>
    <?=$footermenu?>
  </div>

</div>


<script src="<?=admin_assets()?>js/vue.js" type="text/javascript" charset="utf-8"></script> -->
<script src="<?=admin_assets()?>js/vue-components/flexy-blocks.js" type="text/javascript" charset="utf-8"></script>
<script src="<?=admin_assets()?>js/flexyadmin-main.js" type="text/javascript" charset="utf-8"></script>

<!-- // <script src="<?=admin_assets()?>js/build.min.js" type="text/javascript" charset="utf-8"></script> -->

</body>
</html>

