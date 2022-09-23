<div class="calendar-container">

	<div class="calendar-picker">

		<div class="calendar-portal">
		<select class="js-portal-select" name="portal" data-from="<?=session('from')?>" data-to="<?=session('to')?>">
			<option<?php if (PORTAL == 'LR'): ?> selected<?php endif ?>>LR</option>
			<option<?php if (PORTAL == 'MOZ'): ?> selected<?php endif ?>>MOZ</option>
			<option<?php if (PORTAL == 'SWP'): ?> selected<?php endif ?>>SWP</option>
		</select>
		</div>

		<form action="/settimeframe" method="post" class="calendar-timeframe">
			<select name="timeframe" class="js-timeframe">
				<?php if (session('timeframe') == 'Zeitraum'): ?>
				<option>gewählter Zeitraum</option>
				<?php endif; ?>
				<?php foreach (TIMEFRAMES as $timeframe): ?>
				<?php if (session('timeframe') == $timeframe): ?>
				<option selected><?=session('timeframe')?></option>
			<?php elseif(!session('timeframe') AND $timeframe == 'letzte 30 Tage'): ?>
				<option selected><?=$timeframe?></option>
				<?php else: ?>
				<option><?=$timeframe?></option>
				<?php endif; ?>
				<?php endforeach ?>
			</select>
		</form>

		<form method="post" action="/settimeframe" class="calendar-datepicker">
			<fieldset>
				<input type="date" name="from" value="<?=session('from')?>"> -
				<input type="date" name="to" value="<?=session('to')?>">
				<button class="calendar-button" type="submit"></button>
			</fieldset>
			
		</form>


	</div>

</div>
