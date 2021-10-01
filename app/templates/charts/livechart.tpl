<div id="liveChart"></div>

<script>
let liveChartOptions = {
	series: [
		{
			name: 'Pageviews', color: '#6088b4',
			data: [<?=$chart['data']?>],
		},
	],
	chart: {
		height: 400,
		type: 'area',
		toolbar: {show:false},
		zoom: {enabled:false},
		sparkline: {enabled: false},
		stacked: false,
	},



	legend: {
	show:true,
	position: 'top',
	horizontalAlign: 'center',
	onItemHover: {
		highlightDataSeries: false
	},
	floating: true,
	offsetY: 0,
	offsetX: 0,
	},

	stroke: {curve: 'smooth', width: 2},

	dataLabels: {enabled: false,},

	xaxis: {
		categories: [<?=$chart['time']?>],
		crosshairs: {show: true},
		tooltip: {enabled: false},
		tickAmount: 24,		
	},

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},

}
let liveChart = new ApexCharts(document.querySelector("#liveChart"), liveChartOptions);
liveChart.render();
</script>
