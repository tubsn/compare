<div id="radial" ></div>

<script>
let options2 = {
	series: [<?=$pageViewRank?>],
	labels: ['Ressort Durchschnitt', 'Pageviewrank', 'Conversions'],
	chart: {type: 'radialBar',},
	colors:['#6088b4', '#df886d', '#8aafd7'],
	plotOptions: {
		radialBar: {
			//offsetY: -200,
			startAngle: -90,
			endAngle: 90,
			hollow: {
				margin: 10,
				size: '30%',
				//background: 'yellow',
			},
			track: {
				//background: "#ffffff",
				strokeWidth: '97%',
				margin: 5, // margin is in pixels
			},

			dataLabels: {
				name: {fontSize: '22px'},
				value: {fontSize: '16px'},
				total: {
					show: true,
					label: 'Ressort Rank',
					formatter: function (w) {
						return <?=$pageViewRank?> + ' % von 2004'
					}
				}
		    }
		}
	},

	/*
	legend: {
		show: true,
		floating: true,
		fontSize: '16px',
		position: 'right',
		offsetX: 100,
		offsetY: 50,
    },
	*/

};

let chart2 = new ApexCharts(document.querySelector("#radial"), options2);
chart2.render();
//chart2.hideSeries('Pageviewrank');

</script>
