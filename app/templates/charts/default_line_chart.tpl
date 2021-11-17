<div id="Chart-<?=$id?>" class="mb"></div>

<script>
let ChartOptions<?=$id?> = {
	series: [
		{
			name: '<?=$name?>', color: '<?=$color?>',
			data: [<?=$metric?>],
		},
	],
	chart: {
		height: <?=$height ?? 300?>,
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
		categories: [<?=$dimension?>],
		crosshairs: {show: true},
		tooltip: {enabled: false}
	},

	grid: {row: {colors: ['#e5e5e5', 'transparent'], opacity: 0.2}},
	//legend: {show:true},

}

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();
</script>
