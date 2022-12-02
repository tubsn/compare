	<details>
		<summary style="position:absolute; right:0; top:0; cursor:pointer;">Einzeltage einblenden</summary>
			<table class="fancy wide js-sortable" style="margin-bottom:3em">
			<thead>
			<tr>
				<th>Date</th>
				<th>Pageviews</th>
				<th>Besuche</th>
				<th>Conversions</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($stats as $day): ?>
			<tr>
				<td><?=$day['date']?></td>
				<td><?=$day['pageviews']?></td>
				<td><?=$day['sessions']?></td>
				<td><?=$day['conversions']?></td>
			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
	</details>


<div id="chart"></div>

<script>
let chartOptions = {
	series: [
		{
			name: 'Pageviews', color: '#6088b4',
			data: [<?=$chart['pageviews']?>],
		},{
			name: 'Besuche', color: '#8aafd7',
			data: [<?=$chart['sessions']?>],
		},
	],
	chart: {
		height: 300,
		type: 'area',
		toolbar: {show:false},
		zoom: {enabled:false},
		sparkline: {enabled: false},
		stacked: false,

	},

	annotations: {
		yaxis: [{
			y: <?=$ressortAverage-150?>,
			y2: <?=$ressortAverage+150?>,
			fillColor: '#7698bd',
			label: {
				offsetY: 25,
				offsetX: -20,
				text: 'Ø-Ressortdurchschnitt',
			    enabled: true,
			    foreColor: '#fff',
			    borderRadius: 2,
			    padding: 4,
			    opacity: 0.9,
			    borderWidth: 1,
			    borderColor: '#6088b4',
		        style: {
					fontSize: '12px',
		          color: '#fff',
		          background: '#6088b4'
		        },

			}
		}]
	},

	stroke: {curve: 'smooth'},
	dataLabels: {enabled: false,},

	xaxis: {
		categories: [<?=$chart['dates']?>],
		crosshairs: {show: true},
		tooltip: {enabled: false}
	},
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	legend: {show:false},

}
let chart = new ApexCharts(document.querySelector("#chart"), chartOptions);
chart.render();
</script>


<h3 style="margin-bottom:0;">Conversions nach Zeit:</h3>
<small>Abweichungen möglich (nur GA Daten verfügbar)</small>
<div id="conversionChart"></div>

<script>
let conversionChartOptions = {
	series: [
		{
			name: 'Conversions', color: '#df886d',
			data: [<?=$chart['conversions']?>],
		},
	],
	chart: {
		height: 250,
		type: 'area',
		toolbar: {show:false},
		zoom: {enabled:false},
		stacked: true,

	},
	stroke: {
		curve: 'smooth',
	},

	dataLabels: {enabled: false,},

	xaxis: {
		categories: [<?=$chart['dates']?>],
		crosshairs: {show: true},
		tooltip: {enabled: false}
	},

grid: {
  row: {
      colors: ['#e5e5e5', 'transparent'],
      opacity: 0.2
  },

},


}
let conversionChart = new ApexCharts(document.querySelector("#conversionChart"), conversionChartOptions);
conversionChart.render();
</script>
