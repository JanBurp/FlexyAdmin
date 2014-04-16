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
		<?php if (empty($heading)) $heading='Database Error'?>
		<h1><? echo $heading; ?></h1>
    <?php if (IS_LOCALHOST) echo $message; ?>
		<?
		$error=explode(' ',$message);
		$error=substr($error[2],0,4);
		if (empty($error) or $error<'0000' or $error>'9999') $error=mysql_errno();
		switch ($error) {
			case 1045:
			case 2005:
				echo "Set your database information in '".SITEPATH."config/database.php' or '".SITEPATH."config/database_local.php'.";
				break;
			case 1102:
			case 1049:
				echo "Set your database table correct in '".SITEPATH."config/database.php' or '".SITEPATH."config/database_local.php'.<br/><br/>Known databases:<ul>";
				$db_list = mysql_list_dbs();
				while ($row = mysql_fetch_object($db_list)) {
		     echo '<li>'.$row->Database.'</li>';
				}
				echo '</ul>';
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