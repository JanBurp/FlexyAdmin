<?
$sizes=getimagesize($img);
$w=$sizes[0];
$h=$sizes[1]+50;
$size=$sizes[3];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<meta http-equiv="imagetoolbar" content="no" />
	<script language='javascript' type="text/javascript">
	function FitPic(newWidth,newHeight)
	{
		if (self.innerWidth)
		{
			frameWidth = self.innerWidth;
			frameHeight = self.innerHeight;
		}
		else if (document.documentElement && document.documentElement.clientWidth)
		{
			frameWidth = document.documentElement.clientWidth;
			frameHeight = document.documentElement.clientHeight;
		}
		else if (document.body)
		{
			frameWidth = document.body.clientWidth;
			frameHeight = document.body.clientHeight;
		}
		else return;

		if (document.layers)
		{
			tmp1 = parent.outerWidth - parent.innerWidth;
			tmp2 = parent.outerHeight - parent.innerHeight;
			newWidth -= tmp1;
			newHeight -= tmp2;
		}
		parent.window.resizeTo(newWidth,newHeight);
		parent.window.moveTo(self.screen.width/4,self.screen.height/4);
		self.focus();
	};
	</script>
	<style type="text/css">
  <!--
  body {
    background-color: #000000;
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
  }
  img {
    border:none;
  }
  -->
  </style></head>
<body oncontextmenu="return false" ondragstart="return false" onselectstart="return false" onload="FitPic('<? echo $w; ?>','<? echo $h; ?>')">
<a href="javascript:close()"><img src="<? echo $img; ?>" <? echo $size; ?> alt="<? echo $img; ?>" /></a>
</body>
</html>
