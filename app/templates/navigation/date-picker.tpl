<div class="calendar-container">
	<div class="calendar-picker">
		<form action="/settimeframe" method="post">
			<select name="timeframe" class="js-timeframe">
				<?php if (session('timeframe') == 'Zeitraum'): ?>
				<option>gewählter Zeitraum</option>
				<?php endif; ?>
				<?php foreach (TIMEFRAMES as $timeframe): ?>
				<?php if (session('timeframe') == $timeframe): ?>
				<option selected><?=session('timeframe')?></option>
				<?php else: ?>
				<option><?=$timeframe?></option>
				<?php endif; ?>
				<?php endforeach ?>
			</select>
		</form>
		&thinsp;
		<form method="post" action="/settimeframe">
			<fieldset>
				<input type="date" name="from" value="<?=session('from')?>"> -
				<input type="date" name="to" value="<?=session('to')?>">
			</fieldset>
			<button class="calendar-button" type="submit"></button>
		</form>
	</div>
</div>
