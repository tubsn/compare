<div id="ressortChart"></div>

<script>
let chartOptions = {
	series: [
		{
			name: 'Pageviews', color: '#6088b4',
			data: [<?=$pageviews?>],
		},{
			name: 'Besuche', color: '#8aafd7',
			data: [<?=$sessions?>],
		},{
			name: 'Conversions', color: '#df886d',
			data: [<?=$conversions?>],
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

	stroke: {curve: 'smooth'},
	dataLabels: {enabled: false,},

	xaxis: {
		categories: [<?=$dates?>],
		crosshairs: {show: true},
		tooltip: {enabled: false}
	},

	yaxis: [

		{
			axisTicks: {
				show: true,
			},
		},
		{
			show: false,
			axisTicks: {
				show: false,
			},
		},
		{
			axisTicks: {
				show: true,
			},
			opposite: true,
		},



	],

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}, xaxis: {lines: {show: false}}},
	//legend: {show:true},

}
let chart = new ApexCharts(document.querySelector("#ressortChart"), chartOptions);
chart.render();
</script>
