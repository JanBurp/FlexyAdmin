<html>
<body>
	<h1>Activeer account voor <?php echo $identity;?></h1>
	<p>Klik op deze link <?php echo anchor( site_url().'nl/activate?id='.$id.'&activation='.$activation,  'Activate Your Account');?>.</p>
</body>
</html>