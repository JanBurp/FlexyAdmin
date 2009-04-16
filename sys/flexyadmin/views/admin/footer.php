	<div id="footer">
		<div>
			<p id="user">User: <a href="<?=api_url('API_user')?>"><?=$user?>
			<? if (isset($local) and ($local==TRUE)): ?>
				&nbsp;[LOCAL]
			<? endif; ?>
			</a></p>
			<p id="site"><a href="<?=$site;?>" target="_blank"><?=str_replace("http://","",$site);?></a></p>
			<p id="copyright"><a href="admin/info" target="_blank">FlexyAdmin 2009 &nbsp;&nbsp;<span class="small">r<?=$revision?> &copy; 2009</span></a></p>
		</div
	</div>

	<? if ($view!="") $this->load->view($view,$data); ?>

	<div id="ui">
	</div>
	
	<div id="popup">
	</div>
	
	<div id="ui_messages">
		<? foreach ($dialog as $key => $value) : ?>
			<span id="<?=$key?>"><?=$value;?></span>
		<? endforeach; ?>
	</div>

</body>
</html>
