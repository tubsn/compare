<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p style="margin-bottom:2em">
	<b>Hinweise:</b>
	Die vorliegenden Daten enthalten alle Käufe seit dem 23.&thinsp;März 2021 (Einführung Plenigo V3).
	Der Kündigungsstatus für Daten älter als einen Monat ist nicht zu 100% genau. Alle Daten werden aber mindestens einmal am Ende des Monats aktualisiert.
	Die Filterung nach <b>Drive-Segmenten</b> stellt jeweils den Stand des Nutzers <b>zum Zeitpunkt des Kaufs</b> dar. Ressort, Audience und Themengebiet beziehen sich jeweils auf den Artikel, auf dem der Nutzer den Kauf getätigt hat.
</p>

<div class="chart-layout">

<div id="explorer-app">

	<div class="explorer-ui">

		<figure>
		Abo-Bestellzeitraum filtern:
		<fieldset class="explorer-datepicker">
			<input @change="calculateChurn" @input="resetMonth" v-model="from" type="date" name="from">
			<input @change="calculateChurn" @input="resetMonth" v-model="to" type="date" name="to">
			<select @change="filterMonth" v-model="month">
				<option value="">Monat wählen:</option>
				<?php foreach ($months as $month => $dates): ?>
				<option value="<?=$dates['start']?>|<?=$dates['end']?>"><?=$month?></option>
				<?php endforeach; ?>
			</select>
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
			Ursprung:
			<select v-model="origin" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($origins as $originName): ?>
				<option value="<?=$originName?>"><?=$originName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Referer:
			<select v-model="source" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($sources as $sourceName): ?>
				<?php if (empty($sourceName)): ?><?php continue;?><?php endif; ?>
				<option value="<?=$sourceName?>"><?=$sourceName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Referer gruppiert:
			<select v-model="source_grp" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($groupedSources as $groupedSourceName): ?>
				<option value="<?=$groupedSourceName?>"><?=$groupedSourceName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Ressort:
			<select v-model="ressort" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($ressorts as $ressortName): ?>
				<?php if (empty($ressortName)): ?><?php continue;?><?php endif; ?>
				<option value="<?=urlencode($ressortName)?>"><?=ucfirst($ressortName)?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Themengebiet:
			<select v-model="type" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($types as $typeName): ?>
				<?php if (empty($typeName)): ?><?php continue;?><?php endif; ?>
				<option value="<?=urlencode($typeName)?>"><?=$typeName?></option>
				<?php endforeach; ?>
			</select>
			</figure>

			<figure>
			Audience:
			<select v-model="audience" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($audiences as $audienceName): ?>
				<?php if (empty($audienceName)): ?><?php continue;?><?php endif; ?>
				<option value="<?=urlencode($audienceName)?>"><?=$audienceName?></option>
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
			A/B-Testgroup (Drive):
			<select v-model="testgroup" @change="calculateChurn">
				<option value="">kein Filter</option>
				<?php foreach ($testgroups as $testgroupName): ?>
				<option value="<?=$testgroupName?>"><?=$testgroupName?></option>
				<?php endforeach; ?>
			</select>
			</figure>
		<!--
		<figure>
		Chart stauchen:
		<input type="checkbox" checked v-model="compressed" @input="calculateChurn">
		</figure>
		-->

		</div>

		<figure>
		Kündigung innerhalb: <b>{{ days }} Tagen</b>
		<div class="explorer-buttongroup">
			<button @click="setDays(1)">1 Tag</button>
			<button @click="setDays(31)">31 Tagen</button>
			<button @click="setDays(91)">3 Monaten</button>
			<button @click="setDays(121)">6 Monaten</button>
			<button @click="setDays(365)">1 Jahr</button>
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
			<tr title="unter allen Kündigern">
				<td>Ø-Haltedauer:</td>
				<td class="text-right">{{ retention }} T</td>
			</tr>
		</table>

		<p class="text-center"> <span class="greenbg"><b>{{ orders-cancelled }}</b> aktive Kunden</span> <br />nach {{ days-1 }} Tagen</p>

	</div>

</div>

<?php include tpl('orders/explorer/explorer-chart');?>
</div>

<details style="margin-bottom:2em;">
	<summary>Quellen für Referer Gruppierung anzeigen</summary>
<table class="fancy wide align-top">
	<tr>
	<?php foreach ($mapping as $medium => $sources): ?>
		<th><?=$medium?></th>
	<?php endforeach; ?>
	</tr>
	<tr>
	<?php foreach ($mapping as $medium => $sources): ?>
		<td>
			<?php foreach ($sources as $source): ?>
				<?=$source?><br />
			<?php endforeach; ?>
		</td>

	<?php endforeach; ?>
</tr>

</table>
</details>


<style>

.explorer-ui select:focus {background:#e6f2ff;}

.calendar-timeframe, .calendar-datepicker {display:none !important;}

.churnrate {margin:0 auto; text-align:center; margin-bottom:.5em;}
.churn-number {font-size:5em; line-height:100%; font-family:var(--font-highlight); font-weight:bold; color:var(--darkest-red); margin-top:1em;}

.chart-layout {display:grid; grid-template-columns: 1fr 1.5fr; grid-gap:1em;}
@media only screen and (max-width: 1400px) {.chart-layout {display:block;}}

.align-top td {vertical-align:top; }

#Chart-Explorer {position:relative; top:-.7em;}

#explorer-app {display:grid; grid-template-columns: 1.5fr 300px; grid-gap:1em; margin-bottom:1em;}
@media only screen and (min-width: 2000px) {#explorer-app {grid-template-columns: 1.5fr 400px;}}
@media only screen and (max-width: 768px) {#explorer-app {display:block;}}

.explorer-ui, .explorer-results {width:100%; border:1px solid #c4c4c4; padding:1em 1.5em; box-sizing: border-box;}

.explorer-ui figure {margin-bottom:0em;}
.explorer-ui figure:last-of-type {margin-bottom:0em;}
.explorer-ui button {padding:.3em .5em; font-family:var(--font-highlight); font-size:.8em; border-right:0; cursor:pointer;}

.explorer-filter-grid {display:grid; grid-template-columns: 1fr 1fr; grid-gap:1em; margin:1em 0}

.explorer-results {align-self: stretch;}

.explorer-datepicker {box-sizing: border-box; display:flex; background-color:#f0f0f0; padding:0; align-items:center; gap:0.5em; padding:0.4em;}
.explorer-datepicker input {margin:0; cursor:pointer; width:auto; padding:0.2em; min-width:7em;}
.explorer-datepicker select {margin-bottom:0; min-width:130px}
@media only screen and (max-width: 768px) {.explorer-datepicker input, .explorer-datepicker select {min-width:auto;}}

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
