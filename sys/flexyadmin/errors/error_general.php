<html>
<head>
	<title>Error</title>
	<style type="text/css">
		body {
			background-color:	#fff;
			margin: 4px;
			font-family: Lucida Grande, Verdana, Sans-serif;
			font-size: 12px;
			color: #000;
		}
		#content  {
			border:	#696 1px solid;
			background-color:	#fff;
			padding: 10px;
		}
		h1 {
			font-weight: bold;
			font-size: 14px;
			color: #696;
		}
	</style>
</head>
<body>
	<div id="content">
		<h1><? echo $heading; ?></h1>
		<? echo $message; ?>
		<? if (IS_LOCALHOST) backtrace_(3); ?>
	</div>
</body>
</html>