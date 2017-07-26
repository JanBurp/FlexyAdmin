<html>
<head>
	<title>Database Error</title>
	<style type="text/css">
		.error_content {
			background-color:	#EEE;
			font-size: 12px;
			border:	#696 1px solid;
			border-radius:2px;
			padding: 8px;
		}
		.error_content h1 {
			font-weight: bold;
			font-size: 14px;
			color: #696;
		}
		.error_content pre {
			background-color:	#CCC;
			font-size: 10px;
			border:	#999 1px solid;
			border-radius:2px;
			padding: 4px;
		}
	</style>
</head>
<body>
	<div class="error_content">
		<?php if (empty($heading)) $heading='Database Error'?>
		<h1><?php echo $heading; ?></h1>
    
		<?php

		$error = explode(' ',$message);
		$error = substr($error[2],0,4);
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
				if ( preg_match_all('/<p>([^<]*)/u',$message,$matches) ) {
					$lines = $matches[1];
					$error = '';
					foreach ($lines as $key => $line) {
						if (is_sql($line)) {
							$lines[$key] = '<pre><code>'.highlight_code(nice_sql($line)).'</code></pre>';
						}
						$error.= '<p>'.$lines[$key].'</p>';
					}
				}
				echo "Error: '".$error."'<br/>";
        if (ENVIRONMENT=='development' and function_exists('backtrace_')) backtrace_(10);
			break;
		}
		?>
	</div>
</body>
</html>