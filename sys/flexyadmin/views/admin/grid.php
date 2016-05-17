<table class="table table-bordered <?=$class?>" data-table="<?=$table?>" order="<?=$order?>" data-search="<?=htmlentities($search)?>" <?php if (isset($pagination['offset'])):?>offset="<?=$pagination['offset']?>" url="<?=$pagination['base_url']?>"<?php endif;?>>

	<thead>
		<tr class="caption <?=$caption["class"];?>">
			<td colspan="100">
				<table>
					<thead>
						<tr>
							<?php foreach($caption["row"] as $cell): ?>
								<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
							<?php endforeach; ?>
						</tr>
            
            <tr class="hidden extended_search">
              <td colspan="100">
                <div class="extended_search_row">
                  <span class="extended_search_and"><select name="extended_and[]"><option selected="selected" value="OR">OR</option><option value="AND">AND</option></select></span>
                  <span class="extended_search_field"><select name="extended_field[]">
                    <? foreach ($searchfields as $field=>$name) : ?>
                      <option value="<?=$field?>"><?=$name?></option>
                    <? endforeach; ?>
                  </select></span>
                  <span class="extended_search_equal"><select name="extended_equals[]" title="<?=lang('grid_extended_search')?>"><option selected="selected" value="">&asymp;</option><option value="exact">=</option><option value="word">|...|</option></select></span>
                  <span class="extended_search_term"><input name="extended_term[]"></span>
                  <span class="extended_search_plus"><img src="sys/flexyadmin/assets/icons/action_add.gif"></span>
                  <span class="extended_search_remove"><img src="sys/flexyadmin/assets/icons/action_delete.gif"></span>
                </div>
              </td>
            </tr>
            
					</thead>
				</table>
			</td>
		</tr>

	<?php if (isset($heading["row"])): ?>
		<tr class="heading <?=$heading["class"];?>">
		<?php foreach($heading["row"] as $cell): ?>
			<th class="<?=$cell["class"];?>"><?=$cell["cell"];?></th>
		<?php endforeach; ?>
		</tr>
	<?php endif; ?>

	</thead>


	<tbody>
	<?php if (isset($rows) and !empty($rows)):
	 		foreach($rows as $row): ?>
			<tr id="<?=$row["id"]?>" class="<?=$row["class"];?>">
				<?php foreach($row["row"] as $cell): ?>
				<td class="<?=$cell["class"];?>"><?=$cell["cell"];?></td>
				<?php endforeach; ?>
			</tr>
	<?php endforeach; ?>
  <?php endif; ?>
	</tbody>
	
	<?php if (isset($pagination['links'])): ?>
	<tfoot>
		<tr class="pagination">
			<td colspan="100">
				<?=$pagination['links']?>
			</td>
		</tr>
	</tfoot>
	<?php endif; ?>
	
</table>
