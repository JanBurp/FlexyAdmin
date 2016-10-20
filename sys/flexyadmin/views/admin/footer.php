	<div id="footer">
		<div>
			<p id="user">User: <a href="<?=api_url('API_user')?>"><?=$user?>
			<?php if (IS_LOCALHOST): ?>
				&nbsp;[LOCAL]
			<?php endif; ?>
			</a></p>
			<p id="site"><a href="<?=$site;?>" target="_blank"><?=str_replace("http://","",$site);?></a></p>
			<p id="copyright"><a href="admin/info" title="<?=$build?>">FlexyAdmin <?=$version?></span></a> | <a href="admin/info/license"><span class="small">disclaimer</a></span></p>
		</div>
	</div>

</body>
</html>
