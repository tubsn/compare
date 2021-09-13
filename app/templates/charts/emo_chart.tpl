<div id="Chart-<?=$id?>" class="mb"></div>

<script>
let ChartOptions<?=$id?> = {
	  series:
	  	[{ name: '<?=$name?>', data: [<?=$metric?>] }],
	  chart: {type: 'radar', toolbar: {show:false}},

	dataLabels: {enabled: false},
	plotOptions: {
	  radar: {
	    polygons: {
	      strokeColors: '#e9e9e9',
	      fill: {colors: ['#f8f8f8', '#fff']}
	    }
	  }
	},
	/*title: {
	  text: 'Mediatime nach Ressort'
		},*/
	colors: ['<?=$color?>'],
	markers: { size: 4, colors: ['<?=$color?>'], strokeWidth: 2},
	tooltip: {enabled: true, y: {formatter: function(val) {return val}} },
	xaxis: {categories: [<?=$dimension?>]},
	yaxis: {show:false}

}

let Chart<?=$id?> = new ApexCharts(document.querySelector("#Chart-<?=$id?>"), ChartOptions<?=$id?>);
Chart<?=$id?>.render();
</script>
