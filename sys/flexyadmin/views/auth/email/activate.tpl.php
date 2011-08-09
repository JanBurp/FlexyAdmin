<html>
<body>
	<h1>Activate account for <?php echo $identity;?></h1>
	<p>Please click this link to <?php echo anchor( site_url($this->uri->get()).'/'.$id.'/'.$activation, 'Activate Your Account');?>.</p>
</body>
</html>