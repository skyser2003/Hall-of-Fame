
	<form action="<?php e(HOF::url('char', 'action', array('char' => $this->output->char_id))) ?>" method="post">
		<h4>Position & Guarding<a href="<?php e(HOF::url('manual', 'manual', '#posi')) ?>" target="_blank" class="a0">?</a></h4>
		<table>
			<tbody>
				<tr>
					<td>位置(Position) :</td>
					<td>
						<input type="radio" class="vcent" name="position" value="front" <?php e($this->output->char->behavior['position'] == POSITION_FRONT ? ' checked' : '') ?> />
						前衛(Front)
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="radio" class="vcent" name="position" value="back" <?php e($this->output->char->behavior['position'] == POSITION_BACK ? ' checked' : '') ?> />
						後衛(Backs)
					</td>
				</tr>
				<tr>
					<td>護衛(Guarding) :</td>
					<td>
						<select name="guard">
							<?php foreach ($this->output->guard_list as $k => $v): ?>
								<option value="<?php e($k) ?>" <?php e($this->output->char->behavior['guard'] == $k ? ' selected' : '')?> ><?php e($v['info']['desc'])?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="btn" value="Set">
	</form>