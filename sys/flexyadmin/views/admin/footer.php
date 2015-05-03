	<div id="footer">
		<div>
			<p id="user">User: <a href="<?=api_url('API_user')?>"><?=$user?>
			<?php if (IS_LOCALHOST): ?>
				&nbsp;[LOCAL]
			<?php endif; ?>
			</a></p>
			<p id="site"><a href="<?=$site;?>" target="_blank"><?=str_replace("http://","",$site);?></a></p>
			<p id="copyright"><a href="admin/info">FlexyAdmin <span class="small">r<?=$revision?></span></a>|<a href="admin/info/license"><span class="small">disclaimer</a></span></p>
		</div>
	</div>

	<?php if ($view!="") $this->load->view($view,$data); ?>

	<div id="ui">
	</div>
	
	<div id="popup">
	</div>
	
	<div id="help_messages">
		<?php foreach ($help as $key => $value) : ?>
			<span id="<?=$key?>"><?=$value;?></span>
    <?php endforeach; ?>
	</div>

	<div id="ui_messages">
		<?php foreach ($dialog as $key => $value) : ?>
			<span id="<?=$key?>"><?=$value;?></span>
    <?php endforeach; ?>
	</div>

</body>
</html>
