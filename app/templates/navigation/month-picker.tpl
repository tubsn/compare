<div class="calendar-container">

	<div class="calendar-picker">

		<div class="calendar-portal">
		<select class="js-portal-select" name="portal" data-from="<?=session('from')?>" data-to="<?=session('to')?>">
			<option<?php if (PORTAL == 'LR'): ?> selected<?php endif ?>>LR</option>
			<option<?php if (PORTAL == 'MOZ'): ?> selected<?php endif ?>>MOZ</option>
			<option<?php if (PORTAL == 'SWP'): ?> selected<?php endif ?>>SWP</option>
		</select>

		<form action="" method="get" class="calendar-timeframe">
			<select name="month" class="js-calendar-monthpicker">
				<option value="">Monat wählen</option>
				<?php foreach ($months as $month => $dates): ?>
					<?php if ($month == $selectedMonth): ?>
					<option selected value="<?=$month?>"><?=$month?></option>		
					<?php else: ?>
					<option value="<?=$month?>"><?=$month?></option>
					<?php endif ?>
				<?php endforeach; ?>
			</select>
		</form>
		<button class="calendar-button" type="submit"></button>
		</div>


		<script type="text/javascript">
			
					// Timeselector Submit on Change
		let timeframe = document.querySelector('.js-calendar-monthpicker');
		if (timeframe) {
			timeframe.addEventListener('change', () => {timeframe.parentNode.submit();});
		}

		</script>

	</div>
</div>
