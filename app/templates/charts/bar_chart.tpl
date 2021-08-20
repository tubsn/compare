<div id="<?=$barChart['name']?>barChart" class="mb"></div>

<script>
let <?=$barChart['name']?>BarOptions = {
	series: [
		{
			name: '<?=$barChart['name']?>', color: '<?=$barChart['color']?>',
			data: [<?=$barChart['amount']?>],
		}
	],
	chart: {
		type: 'bar',
		toolbar: {show:false},
		height: 300,

	},
	tooltip: {
		enabled: <?php if (isset($barChart['showValues'])): ?>false<?php else: ?>true<?php endif; ?>,
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
		enabled: <?=$barChart['showValues'] ?? 'false'?>,
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
		categories: [<?=$barChart['dates']?>],
		labels: {
			style: {
				fontSize: '16px',
				fontFamily: 'fira sans, sans-serif',
				fontWeight: 400,
			},
		},
	}
};

let <?=$barChart['name']?>barchart = new ApexCharts(document.querySelector("#<?=$barChart['name']?>barChart"), <?=$barChart['name']?>BarOptions);
<?=$barChart['name']?>barchart.render();
</script>
