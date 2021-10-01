<main>

<?php include tpl('navigation/date-picker');?>

<h1>Wertschöpfende Artikel, die <span class="greenbg"> Kunden halten: <b><?=array_sum($spielmacher) + array_sum($abwehr);?></b></span> <span class="conversions">Kunden gewinnen: <b><?=array_sum($spielmacher) + array_sum($stuermer);?></b></span></h1>


<p class="light-box" style="margin-bottom:2em;">
Gesamtproduzierte Artikel: <b><?=array_sum($artikel);?></b> 
&emsp; Geister: <b style="color:#b24646"><?=array_sum($geister);?></b>
&emsp; Abwehr: <b style="color:#779142"><?=array_sum($abwehr);?></b>
&emsp; Stürmer: <b style="color:#abbd86"><?=array_sum($stuermer);?></b>
&emsp; Spielmacher: <b style="color:#44a771"><?=array_sum($spielmacher);?></b>
</p>

<p>
	<b><a href="/valueable/geister">Geister</a>:</b>
	Artikel ohne Conversions und mit weniger als 100 Subscriber Views
	(Sie sorgen weder für Neukunden noch bieten sie relevante Informationen für Bestandskunden)
	
	<br>
	<b><a href="/valueable/abwehr">Abwehr</a>:</b>
	Artikel mit mindestens 100 Subscriber Views
	(Sie halten unsere Abonnenten/Subscriber im Abo, weil Sie relevante Inhalte bieten)
	
	<br>
	<b><a href="/valueable/stuermer">Stürmer</a>:</b>
	Artikel mit mindestens 1 Conversion
	(Sie sorgen für Neukunden)
	
	<br>
	<b><a href="/valueable/spielmacher">Spielmacher</a>:</b>
	Artikel mit mindestens 100 Subscriber Views und mindestens 1 Conversion
	(Sie halten unsere Abonnenten/Subscriber im Abo, weil Sie relevante Inhalte bieten und generieren Neukunden)
	
</p>

<div style="float:right"><button class="js-diagramm-change">Stacked / Non - Stacked</button></div>


<figure style="max-height:100px; overflow:hidden; width:70%;  margin:0 auto;">
	<div id="gesamtChart" class=""></div>
</figure>

<figure>
	<div id="wertschoepfendChart" class="mb"></div>
</figure>

<div class="text-center mb">
<a class="" href="/valueable/geister">Geister-Artikel</a> | 
<a class="" href="/valueable/abwehr">Abwehr-Artikel</a> | 
<a class="" href="/valueable/stuermer">Stürmer-Artikel</a> |
<a class="" href="/valueable/spielmacher">Spielmacher-Artikel</a>
</div>

<hr>

<table class="fancy js-sortable wide">

	<thead>
		<th style="text-align:left">Ressort</th>
		<th style="text-align:right">Gesamt-Artikel</th>
		<th style="text-align:right">Geister</th>
		<th style="text-align:right">Geister-%</th>
		<th style="text-align:right">Abwehr</th>
		<th style="text-align:right">Abwehr-%</th>
		<th style="text-align:right">Stürmer</th>
		<th style="text-align:right">Stürmer-%</th>
		<th style="text-align:right">Spielmacher</th>
		<th style="text-align:right">Spielmacher-%</th>
	</thead>
	<tbody>

<?php foreach ($wertschoepfend as $row): ?>

	<tr style="text-align:right">

	<td style="text-align:left"><a href="/ressort/<?=$row['ressort']?>"><?=ucfirst($row['ressort'])?></a></td>
	<td><?=$row['artikel']?></td>
	<td><?=$row['geister']?></td>
	<td><?=round(($row['geister'] / $row['artikel'] * 100),2)?>&thinsp;%</td>
	<td><?=$row['abwehr']?></td>
	<td><?=round(($row['abwehr'] / $row['artikel'] * 100),2)?>&thinsp;%</td>
	<td><?=$row['stuermer']?></td>	
	<td><?=round(($row['stuermer'] / $row['artikel'] * 100),2)?>&thinsp;%</td>
	<td><?=$row['spielmacher']?></td>
	<td><?=round(($row['spielmacher'] / $row['artikel'] * 100),2)?>&thinsp;%</td>

	</tr>
<?php endforeach ?>

	</tbody>
</table>

<a class="button" href="/export/value">Daten exportieren</a>


<script>
let gesamtChartOptions = {
	series: [

		{
			name: 'Geister', color: '#b24646',
			data: [<?=array_sum($geister);?>],
		},
		{
			name: 'Abwehr', color: '#95b15a',
			data: [<?=array_sum($abwehr);?>],
		},
		{
			name: 'Stürmer', color: '#d7e7b6',
			data: [<?=array_sum($stuermer);?>],
		},
		{
			name: 'Spielmacher', color: '#44a771',
			data: [<?=array_sum($spielmacher);?>],
		},
	],
	chart: {
		type: 'bar',
		stacked: true,
		stackType: '100%',
		toolbar: {show:false},
		height: 100,
	},

	tooltip: {
		enabled: true,
	},

	legend: {show:false},

	grid: {
		show: false,
	},

	plotOptions: {
		bar: {
			horizontal: true,
		}
	},
	dataLabels: {
		textAnchor: 'middle',
		enabled: true,
  		offsetX: 0,
  		offsetY: 0,
		style: {
		  fontSize: '18px',
		  fontFamily: 'fira sans condensed, sans-serif',
		  colors: ['#fff']
		},
		background: {
			enabled: true,
			foreColor: '#444',
			padding: 4,
			borderRadius: 5,
			borderWidth: 0,
			opacity: 0.5,
		},

	},
	xaxis: {
		categories: [''],
		axisBorder: {show:false},
		axisTicks: {show:false},
		labels: {
			show:false,
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	},


};

let gesamtchart = new ApexCharts(document.querySelector("#gesamtChart"), gesamtChartOptions);
gesamtchart.render();


</script>









<script>
let wertschoepfendOptions = {
	series: [

		{
			name: 'Geister', color: '#b24646',
			data: [
			<?php foreach ($geister as $value): ?>
			<?=$value?>,
			<?php endforeach ?>
			],
		},
		{
			name: 'Abwehr', color: '#95b15a',
			data: [
			<?php foreach ($abwehr as $value): ?>
			<?=$value?>,
			<?php endforeach ?>
			],
		},
		{
			name: 'Stürmer', color: '#d7e7b6',
			data: [
			<?php foreach ($stuermer as $value): ?>
			<?=$value?>,
			<?php endforeach ?>
			],
		},
		{
			name: 'Spielmacher', color: '#44a771',
			data: [
			<?php foreach ($spielmacher as $value): ?>
			<?=$value?>,
			<?php endforeach ?>
			],
		},
	],
	chart: {
		type: 'bar',
		stacked: true,
		stackType: '100%',
		toolbar: {show:false},
		height: 550,

	},
	tooltip: {
		enabled: true,
	},

	grid: {
		show: true,
	},
        legend: {
          position: 'top',
          horizontalAlign: 'center'
        },
	plotOptions: {
		bar: {
			borderRadius: 0,
			horizontal: false,
		}
	},

	legend: {position: 'top'},

	dataLabels: {
		textAnchor: 'middle',
		enabled: false,
  		offsetX: 0,
  		offsetY: 20,
		style: {
		  fontSize: '18px',
		  fontFamily: 'fira sans condensed, sans-serif',
		  colors: ['#fff']
		},
		background: {
			enabled: true,
			foreColor: '#444',
			padding: 4,
			borderRadius: 5,
			borderWidth: 0,
			opacity: 0.5,
		},

	},
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	xaxis: {
		categories: [
		<?php foreach ($wertschoepfend as $ressort => $void): ?>		
			'<?=ucfirst($ressort)?>',
		<?php endforeach ?>
		],
		labels: {
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	},


};

let wertchart = new ApexCharts(document.querySelector("#wertschoepfendChart"), wertschoepfendOptions);
wertchart.render();

let changeButton = document.querySelector('.js-diagramm-change');
let chartFlip = false;

changeButton.onclick = function(){

	if (chartFlip) {
		wertchart.updateOptions({chart: {stacked: true, stackType: '100%'}})
		chartFlip = false;
	}
	else {
		wertchart.updateOptions({chart: {stacked: true, stackType: 'normal'}, yaxis: {max: 300},})
		chartFlip = true;
	}

};
</script>





</main>
