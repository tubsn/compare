<main>


<div class="calendar-container">

	<div class="calendar-picker">

		<select class="js-portal-select" name="portal">
			<option<?php if (PORTAL == 'LR'): ?> selected<?php endif ?>>LR</option>
			<option<?php if (PORTAL == 'MOZ'): ?> selected<?php endif ?>>MOZ</option>
			<option<?php if (PORTAL == 'SWP'): ?> selected<?php endif ?>>SWP</option>
		</select>

		<select onchange="window.location.href = '/export/campaigns/' + this.value" class="js-timeframe">
			<option <?php if ($days == 30): ?> selected<?php endif ?> value="30">30 Tage</option>
			<option <?php if ($days == 90): ?> selected<?php endif ?> value="90">90 Tage</option>
			<option <?php if ($days == 365): ?> selected<?php endif ?> value="365">365 Tage</option>
		</select>

		<form>
			<button class="calendar-button" type="submit"></button>
		</form>

	</div>
</div>



<h1><?=$info?></h1>

<style>
table td.narrow {max-width:100%;}
</style>

	<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($data);?>
	<?=dump_table($grouped);?>
</div>



</main>