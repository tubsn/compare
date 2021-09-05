<div id="<?=$singleChart['name']?>Chart"></div>

<script>
let <?=$singleChart['name']?>Options = {
	series: [
		{
			name: '<?=$singleChart['name']?>', color: '<?=$singleChart['color']?>',
			data: [<?=$singleChart['amount']?>],
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
		categories: [<?=$singleChart['dates']?>],
		crosshairs: {show: true},
		tooltip: {enabled: false}
	},

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	//legend: {show:true},

}
let <?=$singleChart['name']?>chart = new ApexCharts(document.querySelector("#<?=$singleChart['name']?>Chart"), <?=$singleChart['name']?>Options);
<?=$singleChart['name']?>chart.render();
</script>
