<div id="combinedarchart" class="mb"></div>

<script>
let combinedarchartOptions = {
	series: [
		<?php foreach ($combinedChart as $chart): ?>
		{
			name: '<?=$chart['name']?>', color: '<?=$chart['color']?>',
			data: [<?=$chart['amount']?>],
		},
		<?php endforeach; ?>
	],
	chart: {
		type: 'bar',
		toolbar: {show:false},
		height: 450,

	},
	tooltip: {
		enabled: <?php if ($combinedChart[0]['showValues'] ?? false): ?> false<?php else: ?>true<?php endif; ?>,
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
	dataLabels: {
		textAnchor: 'middle',
		enabled: <?=$combinedChart[0]['showValues'] ?? 'false'?>,
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
	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}, xaxis: {lines: {show: false}}},
	xaxis: {
		categories: [<?=$combinedChart[0]['dates']?>],
		labels: {
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	},
	yaxis: [

		<?php foreach ($combinedChart as $key => $chart): ?>
		{
			axisTicks: {
				show: true,
			},
			<?php if ($key == 1): ?>opposite: true,<?php endif; ?>
		},
		<?php endforeach; ?>

	],

};

let combinedarchart = new ApexCharts(document.querySelector("#combinedarchart"), combinedarchartOptions);
combinedarchart.render();
</script>
