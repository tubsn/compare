<div id="ressortChart"></div>

<script>
let chartOptions = {
	series: [
		{
			name: 'Klicks', color: '#6088b4',
			data: [<?=$chart['pageviews']?>],
		},{
			name: 'Besuche', color: '#8aafd7',
			data: [<?=$chart['sessions']?>],
		},{
			name: 'Conversions', color: '#df886d',
			data: [<?=$chart['conversions']?>],
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
		categories: [<?=$chart['dates']?>],
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

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	//legend: {show:true},

}
let chart = new ApexCharts(document.querySelector("#ressortChart"), chartOptions);
chart.render();
</script>
