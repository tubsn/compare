
<!--
<div style="line-height:0; position:relative;">
	<img style="position:absolute;top:-8px; left:189px;" src="/styles/img/reenderhat.png">
</div>
-->
<div id="radialPageviews" ></div>

<script>
let ressortRankOptions = {
	series: [<?=$ressortRank?>],
	labels: ['Ressort Durchschnitt'],
	chart: {type: 'radialBar', width:800,},
	colors:['#6088b4'],
	plotOptions: {
		radialBar: {
			offsetY: -100,
			startAngle: -90,
			endAngle: 90,
			hollow: {
				margin: 10,
				size: '30%',
			},
			track: {
				strokeWidth: '90%',
				margin: 5, // margin is in pixels
			},

			dataLabels: {
				name: {fontSize: '22px', offsetY: 30,},
				value: {fontSize: '60px', offsetY: -20, color:'#6088b4',},
				total: {
					show: true,
					label: 'Pageviews',
					formatter: function (w) {
						return <?=$article['pageviews']?>
					}
				}
		    }
		}
	},

};

let ressortRank = new ApexCharts(document.querySelector("#radialPageviews"), ressortRankOptions);
ressortRank.render();

</script>