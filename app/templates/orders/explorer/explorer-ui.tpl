<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<div class="chart-layout">

<div id="explorer-app">

	<div class="explorer-ui">

		<figure>
		Abo-Bestellzeitraum filtern:
		<fieldset class="explorer-datepicker">
			<input @change="calculateChurn" v-model="from" type="date" name="from">
			- <input @change="calculateChurn" v-model="to" type="date" name="to">
			<!--<button @click="calculateChurn">filtern</button>-->
		</fieldset>
		</figure>

		<div class="explorer-filter-grid">

			<figure>
			Produkt:
			<select v-model="product" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($products as $productName): ?>
				<option value="<?=urlencode($productName)?>"><?=$productName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Drive-Segment:
			<select v-model="segment" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($segments as $segmentName): ?>
				<option value="<?=$segmentName?>"><?=$segmentName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Ressort:
			<select v-model="ressort" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($ressorts as $ressortName): ?>
				<?php if (empty($ressortName)): ?><?php continue;?><?php endif; ?>
				<option value="<?=$ressortName?>"><?=ucfirst($ressortName)?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Abo-Quelle:
			<select v-model="origin" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($origins as $originName): ?>
				<option value="<?=$originName?>"><?=$originName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

		</div>

		<figure>
		Kündigung innerhalb: <b>{{ days }} Tagen</b>
		<div class="explorer-buttongroup">
			<button @click="setDays(1)">1 Tag</button>
			<button @click="setDays(31)">31 Tagen</button>
			<button @click="setDays(91)">3 Monaten</button>
			<button @click="setDays(365)">1Jahr</button>
			<button @click="setDays(1000)">Max</button>
		</div>

		<input v-model="days" @change="calculateChurn" class="range-slider" type="range" min="1" max="180" name="fontsize"/>
		</figure>


	</div>

	<div class="explorer-results">

		<div class="churnrate">
			<div class="churn-number">{{ quote }}%</div>
			<div class="churn-text">Churnrate</div>
		</div>

		<table class="fancy wide">
			<tr>
				<td>Bestellungen:</td>
				<td class="text-right">{{ orders }}</td>
			</tr>
			<tr>
				<td>Kündigungen:</td>
				<td class="text-right">{{ cancelled }}</td>
			</tr>
		</table>

		<p class="text-center"> <span class="greenbg"><b>{{ orders-cancelled }}</b> aktive Kunden</span> <br />nach {{ days-1 }} Tagen</p>

	</div>

	<div>


	</div>

</div>

<?php include tpl('charts/churn-explorer');?>
</div>

<style>

.calendar-timeframe, .calendar-datepicker {display:none !important;}

.churnrate {margin:0 auto; text-align:center; margin-bottom:.5em;}
.churn-number {font-size:5em; line-height:100%; font-family:var(--font-highlight); font-weight:bold; color:var(--darkest-red); margin-top:.3em;}

.chart-layout {display:grid; grid-template-columns: 1fr 1.5fr; grid-gap:1em;}
#Chart-Explorer {position:relative; top:-.7em;}

#explorer-app {display:grid; grid-template-columns: 1.5fr 1fr; grid-gap:1em;}
.explorer-ui, .explorer-results {width:100%; border:1px solid #c4c4c4; padding:1em 1.5em; box-sizing: border-box;}

.explorer-ui figure {margin-bottom:0em;}
.explorer-ui figure:last-of-type {margin-bottom:0em;}
.explorer-ui button {padding:.3em .5em; font-family:var(--font-highlight); font-size:.8em; border-right:0; cursor:pointer;}

.explorer-filter-grid {display:grid; grid-template-columns: 1fr 1fr; grid-gap:1em; margin:1em 0}

.explorer-results {align-self: stretch;}

.explorer-datepicker {width:300px; display:flex; background-color:#f0f0f0; padding:0.4em; align-items:center;}
.explorer-datepicker input {margin:0; margin-left:0.4em; margin-right:0.4em; cursor:pointer; width:auto; }

.explorer-buttongroup button:first-of-type {border-top-left-radius:.3em; border-bottom-left-radius:.3em;}
.explorer-buttongroup button:last-of-type {border-top-right-radius:.3em; border-bottom-right-radius:.3em;}

input[type=range].range-slider {width: 100%; margin: 5.5px 0; border:0; margin-bottom:1em; background-color: transparent; -webkit-appearance: none; margin-bottom:0;}
input[type=range].range-slider:focus {outline: none;}
input[type=range].range-slider::-webkit-slider-runnable-track {background: rgb(181, 181, 181); width: 100%; height: 7px; cursor: pointer; border-radius: 50px;}
input[type=range].range-slider::-webkit-slider-thumb {margin-top: -5.6px; width: 25px; height: 25px; background: #213e5e; border: 0; border-radius: 50px; cursor: pointer; -webkit-appearance: none;}
input[type=range].range-slider:focus::-webkit-slider-runnable-track {background: #45241f;}
input[type=range].range-slider::-moz-range-track {background: rgb(181, 181, 181); width: 100%; height: 7px; cursor: pointer; border-radius: 50px;}
input[type=range].range-slider::-moz-range-thumb {width: 25px; height: 25px; background: #213e5e; border: 0; border-radius: 50px; cursor: pointer;}

</style>

</main>

<?php include tpl('orders/explorer/churnscript');?>
