<div id="conversionIndexChart" ></div>

<script>
let conversionIndexChartOptions = {
	series: [<?=$article['conversions']*10?>],
	labels: ['Ressort Durchschnitt'],
	chart: {type: 'radialBar', width:800,},
	colors:['#df886d'],
	plotOptions: {
		radialBar: {
			offsetY: -290,
			startAngle: 90,
			endAngle: -90,
			hollow: {
				margin: 10,
				size: '30%',
			},
			track: {
				strokeWidth: '90%',
				margin: 5, // margin is in pixels
			},

			dataLabels: {
				name: {fontSize: '50px'},
				value: {fontSize: '80px', offsetY: 60, color:'#df886d',},
				total: {
					show: true,
					label: 'Conversions',
					formatter: function (w) {
						return '<?=$article['conversions']?>'
					}
				}
		    }
		}
	},

};

let conversionIndexChart = new ApexCharts(document.querySelector("#conversionIndexChart"), conversionIndexChartOptions);
conversionIndexChart.render();

</script>