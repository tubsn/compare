<div id="Chart-<?=$id?>" class="mb"></div>

<script>
let ChartOptions<?=$id?> = {
	series: [
		{
			name: '<?=$name?>', color: '<?=$color?>',
			data: [<?=$metric?>],
		}
	],
	chart: {
		type: 'bar',
		toolbar: {show:false},
		height: <?=$height ?? 300?>,

	},
	tooltip: {
		enabled: <?php if (isset($showValues)): ?>false<?php else: ?>true<?php endif; ?>,
	},

	grid: {
		show: true,
	},

	plotOptions: {
		bar: {
			borderRadius: 0,
			horizontal: false,
		}
	},
	dataLabels: {
		textAnchor: 'middle',
		enabled: <?=$showValues ?? 'false'?>,
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
		categories: [<?=$dimension?>],
		labels: {
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	}
};

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();
</script>
