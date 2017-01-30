<div class="card grid graph">
  <h1 class="card-header"><?=$title?></h1>

  <div class="card-block table-responsive">
    <table class="table table-bordered table-hover table-sm">

    	<tbody>
    	<?php if (isset($rows) and !empty($rows)): ?>
    	  <tr>
          <?php foreach ($rows as $key => $value): ?>
    			<td class='bar'>
    				<div class="graph-bar-top" style="height:<?=110-$value['row'][1]['percentage']?>px"></div>
    				<div class="graph-bar" style="height:<?=$value['row'][1]['percentage']?>px">
    					<span class="vertical-text" title="<?=$value['row'][1]['value'];?>"><?=$value['row'][1]['value'];?></span>
    				</div>
    			</td>
          <?php endforeach ?>
    		</tr>
    		<tr>
          <?php foreach ($rows as $key => $value): ?>
            <td class='<?=$value['class']?>'><?=$value['row'][0]['value'];?></td>
          <?php endforeach ?>
    		</tr>
      <?php endif ?>
      </tbody>
    </table>
    
  </div>
</div>
    