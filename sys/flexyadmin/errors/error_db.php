<html>
<head>
	<title>Database Error</title>
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
		<?
		$error=explode(' ',$message);
		$error=substr($error[2],0,4);
		if (empty($error) or $error<'0000' or $error>'9999') $error=mysql_errno();
		switch ($error) {
			case 1045:
				echo "Set your database information in 'site/database.php' or 'site/database_local.php'.";
				break;
			case 1102:
				echo "Set your database table correct in 'site/database.php' or 'site/database_local.php'.";
				break;

			default:
				echo "Error: '".$error."'<br/>";
				if (IS_LOCALHOST) backtrace_(3);
			break;
		}
		?>
	</div>
</body>
</html>